<?php

namespace WPStack_Connect_Vendor\Aws\S3;

use WPStack_Connect_Vendor\Aws\Arn\ArnParser;
use WPStack_Connect_Vendor\Aws\Arn\ObjectLambdaAccessPointArn;
use WPStack_Connect_Vendor\Aws\ClientResolver;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Aws\Endpoint\EndpointProvider;
use WPStack_Connect_Vendor\Aws\Endpoint\PartitionEndpointProvider;
use WPStack_Connect_Vendor\GuzzleHttp\Exception\InvalidArgumentException;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Uri;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Used to update the URL used for S3 requests to support:
 * S3 Accelerate, S3 DualStack or Both. It will build to
 * host style paths unless specified, including for S3
 * DualStack.
 *
 * IMPORTANT: this middleware must be added after the "build" step.
 *
 * @internal
 */
class S3EndpointMiddleware
{
    private static $exclusions = ['CreateBucket' => \true, 'DeleteBucket' => \true, 'ListBuckets' => \true];
    const NO_PATTERN = 0;
    const DUALSTACK = 1;
    const ACCELERATE = 2;
    const ACCELERATE_DUALSTACK = 3;
    const PATH_STYLE = 4;
    const HOST_STYLE = 5;
    /** @var bool */
    private $accelerateByDefault;
    /** @var bool */
    private $dualStackByDefault;
    /** @var bool */
    private $pathStyleByDefault;
    /** @var string */
    private $region;
    /** @var callable */
    private $endpointProvider;
    /** @var callable */
    private $nextHandler;
    /** @var string */
    private $endpoint;
    /**
     * Create a middleware wrapper function
     *
     * @param string $region
     * @param EndpointProvider $endpointProvider
     * @param array  $options
     *
     * @return callable
     */
    public static function wrap($region, $endpointProvider, array $options)
    {
        return function (callable $handler) use($region, $endpointProvider, $options) {
            return new self($handler, $region, $options, $endpointProvider);
        };
    }
    public function __construct(callable $nextHandler, $region, array $options, $endpointProvider = null)
    {
        $this->pathStyleByDefault = isset($options['path_style']) ? (bool) $options['path_style'] : \false;
        $this->dualStackByDefault = isset($options['dual_stack']) ? (bool) $options['dual_stack'] : \false;
        $this->accelerateByDefault = isset($options['accelerate']) ? (bool) $options['accelerate'] : \false;
        $this->region = (string) $region;
        $this->endpoint = isset($options['endpoint']) ? $options['endpoint'] : "";
        $this->endpointProvider = \is_null($endpointProvider) ? \WPStack_Connect_Vendor\Aws\Endpoint\PartitionEndpointProvider::defaultProvider() : $endpointProvider;
        $this->nextHandler = $nextHandler;
    }
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        if (!empty($this->endpoint)) {
            $request = $this->applyEndpoint($command, $request);
        } else {
            switch ($this->endpointPatternDecider($command, $request)) {
                case self::HOST_STYLE:
                    $request = $this->applyHostStyleEndpoint($command, $request);
                    break;
                case self::NO_PATTERN:
                    break;
                case self::PATH_STYLE:
                    $request = $this->applyPathStyleEndpointCustomizations($command, $request);
                    break;
                case self::DUALSTACK:
                    $request = $this->applyDualStackEndpoint($command, $request);
                    break;
                case self::ACCELERATE:
                    $request = $this->applyAccelerateEndpoint($command, $request, 's3-accelerate');
                    break;
                case self::ACCELERATE_DUALSTACK:
                    $request = $this->applyAccelerateEndpoint($command, $request, 's3-accelerate.dualstack');
                    break;
            }
        }
        $nextHandler = $this->nextHandler;
        return $nextHandler($command, $request);
    }
    private static function isRequestHostStyleCompatible(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        return \WPStack_Connect_Vendor\Aws\S3\S3Client::isBucketDnsCompatible($command['Bucket']) && ($request->getUri()->getScheme() === 'http' || \strpos($command['Bucket'], '.') === \false) && \filter_var($request->getUri()->getHost(), \FILTER_VALIDATE_IP) === \false;
    }
    private function endpointPatternDecider(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        $accelerate = isset($command['@use_accelerate_endpoint']) ? $command['@use_accelerate_endpoint'] : $this->accelerateByDefault;
        $dualStack = isset($command['@use_dual_stack_endpoint']) ? $command['@use_dual_stack_endpoint'] : $this->dualStackByDefault;
        $pathStyle = isset($command['@use_path_style_endpoint']) ? $command['@use_path_style_endpoint'] : $this->pathStyleByDefault;
        if ($accelerate && $dualStack) {
            // When try to enable both for operations excluded from s3-accelerate,
            // only dualstack endpoints will be enabled.
            return $this->canAccelerate($command) ? self::ACCELERATE_DUALSTACK : self::DUALSTACK;
        }
        if ($accelerate && $this->canAccelerate($command)) {
            return self::ACCELERATE;
        }
        if ($dualStack) {
            return self::DUALSTACK;
        }
        if (!$pathStyle && self::isRequestHostStyleCompatible($command, $request)) {
            return self::HOST_STYLE;
        }
        return self::PATH_STYLE;
    }
    private function canAccelerate(\WPStack_Connect_Vendor\Aws\CommandInterface $command)
    {
        return empty(self::$exclusions[$command->getName()]) && \WPStack_Connect_Vendor\Aws\S3\S3Client::isBucketDnsCompatible($command['Bucket']);
    }
    private function getBucketStyleHost(\WPStack_Connect_Vendor\Aws\CommandInterface $command, $host)
    {
        // For operations on the base host (e.g. ListBuckets)
        if (!isset($command['Bucket'])) {
            return $host;
        }
        return "{$command['Bucket']}.{$host}";
    }
    private function applyHostStyleEndpoint(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        $uri = $request->getUri();
        $request = $request->withUri($uri->withHost($this->getBucketStyleHost($command, $uri->getHost()))->withPath($this->getBucketlessPath($uri->getPath(), $command)));
        return $request;
    }
    private function applyPathStyleEndpointCustomizations(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        if ($command->getName() == 'WriteGetObjectResponse') {
            $dnsSuffix = $this->endpointProvider->getPartition($this->region, 's3')->getDnsSuffix();
            $fips = \WPStack_Connect_Vendor\Aws\is_fips_pseudo_region($this->region) ? "-fips" : "";
            $region = \WPStack_Connect_Vendor\Aws\strip_fips_pseudo_regions($this->region);
            $host = "{$command['RequestRoute']}.s3-object-lambda{$fips}.{$region}.{$dnsSuffix}";
            $uri = $request->getUri();
            $request = $request->withUri($uri->withHost($host)->withPath($this->getBucketlessPath($uri->getPath(), $command)));
        }
        return $request;
    }
    private function applyDualStackEndpoint(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        $request = $request->withUri($request->getUri()->withHost($this->getDualStackHost()));
        if (empty($command['@use_path_style_endpoint']) && !$this->pathStyleByDefault && self::isRequestHostStyleCompatible($command, $request)) {
            $request = $this->applyHostStyleEndpoint($command, $request);
        }
        return $request;
    }
    private function getDualStackHost()
    {
        $dnsSuffix = $this->endpointProvider->getPartition($this->region, 's3')->getDnsSuffix();
        return "s3.dualstack.{$this->region}.{$dnsSuffix}";
    }
    private function applyAccelerateEndpoint(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, $pattern)
    {
        $request = $request->withUri($request->getUri()->withHost($this->getAccelerateHost($command, $pattern))->withPath($this->getBucketlessPath($request->getUri()->getPath(), $command)));
        return $request;
    }
    private function getAccelerateHost(\WPStack_Connect_Vendor\Aws\CommandInterface $command, $pattern)
    {
        $dnsSuffix = $this->endpointProvider->getPartition($this->region, 's3')->getDnsSuffix();
        return "{$command['Bucket']}.{$pattern}.{$dnsSuffix}";
    }
    private function getBucketlessPath($path, \WPStack_Connect_Vendor\Aws\CommandInterface $command)
    {
        $pattern = '/^\\/' . \preg_quote($command['Bucket'], '/') . '/';
        $path = \preg_replace($pattern, '', $path) ?: '/';
        if (\substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        return $path;
    }
    private function applyEndpoint(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request)
    {
        $dualStack = isset($command['@use_dual_stack_endpoint']) ? $command['@use_dual_stack_endpoint'] : $this->dualStackByDefault;
        if (\WPStack_Connect_Vendor\Aws\Arn\ArnParser::isArn($command['Bucket'])) {
            $arn = \WPStack_Connect_Vendor\Aws\Arn\ArnParser::parse($command['Bucket']);
            $outpost = $arn->getService() == 's3-outposts';
            if ($outpost && $dualStack) {
                throw new \WPStack_Connect_Vendor\GuzzleHttp\Exception\InvalidArgumentException("Outposts + dualstack is not supported");
            }
            if ($arn instanceof \WPStack_Connect_Vendor\Aws\Arn\ObjectLambdaAccessPointArn) {
                return $request;
            }
        }
        if ($dualStack) {
            throw new \WPStack_Connect_Vendor\GuzzleHttp\Exception\InvalidArgumentException("Custom Endpoint + Dualstack not supported");
        }
        if ($command->getName() == 'WriteGetObjectResponse') {
            $host = "{$command['RequestRoute']}.{$this->endpoint}";
            $uri = $request->getUri();
            return $request = $request->withUri($uri->withHost($host)->withPath($this->getBucketlessPath($uri->getPath(), $command)));
        }
        $host = $this->pathStyleByDefault ? $this->endpoint : $this->getBucketStyleHost($command, $this->endpoint);
        $uri = $request->getUri();
        $scheme = $uri->getScheme();
        if (empty($scheme)) {
            $request = $request->withUri($uri->withHost($host));
        } else {
            $request = $request->withUri($uri);
        }
        return $request;
    }
}
