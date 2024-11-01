<?php

namespace WPStack_Connect_Vendor\Aws\S3Control;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Arn\AccessPointArnInterface;
use WPStack_Connect_Vendor\Aws\Arn\ArnInterface;
use WPStack_Connect_Vendor\Aws\Arn\ArnParser;
use WPStack_Connect_Vendor\Aws\Arn\Exception\InvalidArnException;
use WPStack_Connect_Vendor\Aws\Arn\S3\BucketArnInterface;
use WPStack_Connect_Vendor\Aws\Arn\S3\OutpostsArnInterface;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Aws\Endpoint\PartitionEndpointProvider;
use WPStack_Connect_Vendor\Aws\Exception\InvalidRegionException;
use WPStack_Connect_Vendor\Aws\Exception\UnresolvedEndpointException;
use WPStack_Connect_Vendor\Aws\S3\EndpointRegionHelperTrait;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Checks for access point ARN in members targeting BucketName, modifying
 * endpoint as appropriate
 *
 * @internal
 */
class EndpointArnMiddleware
{
    use EndpointRegionHelperTrait;
    /**
     * Commands which do not do ARN expansion for a specific given shape name
     * @var array
     */
    private static $selectiveNonArnableCmds = ['AccessPointName' => ['CreateAccessPoint'], 'BucketName' => []];
    /**
     * Commands which do not do ARN expansion at all for relevant members
     * @var array
     */
    private static $nonArnableCmds = ['CreateBucket', 'ListRegionalBuckets'];
    /**
     * Commands which trigger endpoint and signer redirection based on presence
     * of OutpostId
     * @var array
     */
    private static $outpostIdRedirectCmds = ['CreateBucket', 'ListRegionalBuckets'];
    /** @var callable */
    private $nextHandler;
    /** @var boolean */
    private $isUseEndpointV2;
    /**
     * Create a middleware wrapper function.
     *
     * @param Service $service
     * @param $region
     * @param array $config
     * @return callable
     */
    public static function wrap(\WPStack_Connect_Vendor\Aws\Api\Service $service, $region, array $config, $isUseEndpointV2)
    {
        return function (callable $handler) use($service, $region, $config, $isUseEndpointV2) {
            return new self($handler, $service, $region, $config, $isUseEndpointV2);
        };
    }
    public function __construct(callable $nextHandler, \WPStack_Connect_Vendor\Aws\Api\Service $service, $region, array $config = [], $isUseEndpointV2 = \false)
    {
        $this->partitionProvider = \WPStack_Connect_Vendor\Aws\Endpoint\PartitionEndpointProvider::defaultProvider();
        $this->region = $region;
        $this->service = $service;
        $this->config = $config;
        $this->nextHandler = $nextHandler;
        $this->isUseEndpointV2 = $isUseEndpointV2;
    }
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $cmd, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $req)
    {
        $nextHandler = $this->nextHandler;
        $op = $this->service->getOperation($cmd->getName())->toArray();
        if (!empty($op['input']['shape']) && !\in_array($cmd->getName(), self::$nonArnableCmds)) {
            $service = $this->service->toArray();
            if (!empty($input = $service['shapes'][$op['input']['shape']])) {
                // Stores member name that targets 'BucketName' shape
                $bucketNameMember = null;
                // Stores member name that targets 'AccessPointName' shape
                $accesspointNameMember = null;
                foreach ($input['members'] as $key => $member) {
                    if ($member['shape'] === 'BucketName') {
                        $bucketNameMember = $key;
                    }
                    if ($member['shape'] === 'AccessPointName') {
                        $accesspointNameMember = $key;
                    }
                }
                // Determine if appropriate member contains ARN value and is
                // eligible for ARN expansion
                if (!\is_null($bucketNameMember) && !empty($cmd[$bucketNameMember]) && !\in_array($cmd->getName(), self::$selectiveNonArnableCmds['BucketName']) && \WPStack_Connect_Vendor\Aws\Arn\ArnParser::isArn($cmd[$bucketNameMember])) {
                    $arn = \WPStack_Connect_Vendor\Aws\Arn\ArnParser::parse($cmd[$bucketNameMember]);
                    !$this->isUseEndpointV2 && ($partition = $this->validateBucketArn($arn));
                } elseif (!\is_null($accesspointNameMember) && !empty($cmd[$accesspointNameMember]) && !\in_array($cmd->getName(), self::$selectiveNonArnableCmds['AccessPointName']) && \WPStack_Connect_Vendor\Aws\Arn\ArnParser::isArn($cmd[$accesspointNameMember])) {
                    $arn = \WPStack_Connect_Vendor\Aws\Arn\ArnParser::parse($cmd[$accesspointNameMember]);
                    !$this->isUseEndpointV2 && ($partition = $this->validateAccessPointArn($arn));
                }
                // Process only if an appropriate member contains an ARN value
                // and is an Outposts ARN
                if (!empty($arn) && $arn instanceof \WPStack_Connect_Vendor\Aws\Arn\S3\OutpostsArnInterface) {
                    if (!$this->isUseEndpointV2) {
                        // Generate host based on ARN
                        $host = $this->generateOutpostsArnHost($arn, $req);
                        $req = $req->withHeader('x-amz-outpost-id', $arn->getOutpostId());
                    }
                    // ARN replacement
                    $path = $req->getUri()->getPath();
                    if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\AccessPointArnInterface) {
                        // Replace ARN with access point name
                        $path = \str_replace(\urlencode($cmd[$accesspointNameMember]), $arn->getAccesspointName(), $path);
                        // Replace ARN in the payload
                        $req->getBody()->seek(0);
                        $body = \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor(\str_replace($cmd[$accesspointNameMember], $arn->getAccesspointName(), $req->getBody()->getContents()));
                        // Replace ARN in the command
                        $cmd[$accesspointNameMember] = $arn->getAccesspointName();
                    } elseif ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\S3\BucketArnInterface) {
                        // Replace ARN in the path
                        $path = \str_replace(\urlencode($cmd[$bucketNameMember]), $arn->getBucketName(), $path);
                        // Replace ARN in the payload
                        $req->getBody()->seek(0);
                        $newBody = \str_replace($cmd[$bucketNameMember], $arn->getBucketName(), $req->getBody()->getContents());
                        $body = \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor($newBody);
                        // Replace ARN in the command
                        $cmd[$bucketNameMember] = $arn->getBucketName();
                    }
                    // Validate or set account ID in command
                    if (isset($cmd['AccountId'])) {
                        if ($cmd['AccountId'] !== $arn->getAccountId()) {
                            throw new \InvalidArgumentException("The account ID" . " supplied in the command ({$cmd['AccountId']})" . " does not match the account ID supplied in the" . " ARN (" . $arn->getAccountId() . ").");
                        }
                    } else {
                        $cmd['AccountId'] = $arn->getAccountId();
                    }
                    // Set modified request
                    if (isset($body)) {
                        $req = $req->withBody($body);
                    }
                    if ($this->isUseEndpointV2) {
                        $req = $req->withUri($req->getUri()->withPath($path));
                        goto next;
                    }
                    $req = $req->withUri($req->getUri()->withHost($host)->withPath($path))->withHeader('x-amz-account-id', $arn->getAccountId());
                    // Update signing region based on ARN data if configured to do so
                    if ($this->config['use_arn_region']->isUseArnRegion()) {
                        $region = $arn->getRegion();
                    } else {
                        $region = $this->region;
                    }
                    $endpointData = $partition(['region' => $region, 'service' => $arn->getService()]);
                    $cmd['@context']['signing_region'] = $endpointData['signingRegion'];
                    // Update signing service for Outposts ARNs
                    if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\S3\OutpostsArnInterface) {
                        $cmd['@context']['signing_service'] = $arn->getService();
                    }
                }
            }
        }
        if ($this->isUseEndpointV2) {
            goto next;
        }
        // For operations that redirect endpoint & signing service based on
        // presence of OutpostId member. These operations will likely not
        // overlap with operations that perform ARN expansion.
        if (\in_array($cmd->getName(), self::$outpostIdRedirectCmds) && !empty($cmd['OutpostId'])) {
            $req = $req->withUri($req->getUri()->withHost($this->generateOutpostIdHost()));
            $cmd['@context']['signing_service'] = 's3-outposts';
        }
        next:
        return $nextHandler($cmd, $req);
    }
    private function generateOutpostsArnHost(\WPStack_Connect_Vendor\Aws\Arn\S3\OutpostsArnInterface $arn, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $req)
    {
        if (!empty($this->config['use_arn_region']->isUseArnRegion())) {
            $region = $arn->getRegion();
        } else {
            $region = $this->region;
        }
        $fipsString = $this->config['use_fips_endpoint']->isUseFipsEndpoint() ? "-fips" : "";
        $suffix = $this->getPartitionSuffix($arn, $this->partitionProvider);
        return "s3-outposts{$fipsString}.{$region}.{$suffix}";
    }
    private function generateOutpostIdHost()
    {
        $partition = $this->partitionProvider->getPartition($this->region, $this->service->getEndpointPrefix());
        $suffix = $partition->getDnsSuffix();
        return "s3-outposts.{$this->region}.{$suffix}";
    }
    private function validateBucketArn(\WPStack_Connect_Vendor\Aws\Arn\ArnInterface $arn)
    {
        if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\S3\BucketArnInterface) {
            return $this->validateArn($arn);
        }
        throw new \WPStack_Connect_Vendor\Aws\Arn\Exception\InvalidArnException('Provided ARN was not a valid S3 bucket' . ' ARN.');
    }
    private function validateAccessPointArn(\WPStack_Connect_Vendor\Aws\Arn\ArnInterface $arn)
    {
        if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\AccessPointArnInterface) {
            return $this->validateArn($arn);
        }
        throw new \WPStack_Connect_Vendor\Aws\Arn\Exception\InvalidArnException('Provided ARN was not a valid S3 access' . ' point ARN.');
    }
    /**
     * Validates an ARN, returning a partition object corresponding to the ARN
     * if successful
     *
     * @param $arn
     * @return \Aws\Endpoint\Partition
     */
    private function validateArn(\WPStack_Connect_Vendor\Aws\Arn\ArnInterface $arn)
    {
        // Dualstack is not supported with Outposts ARNs
        if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\S3\OutpostsArnInterface && !empty($this->config['dual_stack'])) {
            throw new \WPStack_Connect_Vendor\Aws\Exception\UnresolvedEndpointException('Dualstack is currently not supported with S3 Outposts ARNs.' . ' Please disable dualstack or do not supply an Outposts ARN.');
        }
        // Get partitions for ARN and client region
        $arnPart = $this->partitionProvider->getPartitionByName($arn->getPartition());
        $clientPart = $this->partitionProvider->getPartition($this->region, 's3');
        // If client partition not found, try removing pseudo-region qualifiers
        if (!$clientPart->isRegionMatch($this->region, 's3')) {
            $clientPart = $this->partitionProvider->getPartition(\WPStack_Connect_Vendor\Aws\strip_fips_pseudo_regions($this->region), 's3');
        }
        // Verify that the partition matches for supplied partition and region
        if ($arn->getPartition() !== $clientPart->getName()) {
            throw new \WPStack_Connect_Vendor\Aws\Exception\InvalidRegionException('The supplied ARN partition' . " does not match the client's partition.");
        }
        if ($clientPart->getName() !== $arnPart->getName()) {
            throw new \WPStack_Connect_Vendor\Aws\Exception\InvalidRegionException('The corresponding partition' . ' for the supplied ARN region does not match the' . " client's partition.");
        }
        // Ensure ARN region matches client region unless
        // configured for using ARN region over client region
        $this->validateMatchingRegion($arn);
        // Ensure it is not resolved to fips pseudo-region for S3 Outposts
        $this->validateFipsConfigurations($arn);
        return $arnPart;
    }
}
