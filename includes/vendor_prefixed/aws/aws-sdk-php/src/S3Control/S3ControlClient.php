<?php

namespace WPStack_Connect_Vendor\Aws\S3Control;

use WPStack_Connect_Vendor\Aws\AwsClient;
use WPStack_Connect_Vendor\Aws\CacheInterface;
use WPStack_Connect_Vendor\Aws\HandlerList;
use WPStack_Connect_Vendor\Aws\S3\UseArnRegion\Configuration;
use WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationInterface;
use WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationProvider as UseArnRegionConfigurationProvider;
use WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface;
/**
 * This client is used to interact with the **AWS S3 Control** service.
 * @method \Aws\Result createAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAccessPointAsync(array $args = [])
 * @method \Aws\Result createAccessPointForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAccessPointForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result createBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBucketAsync(array $args = [])
 * @method \Aws\Result createJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createJobAsync(array $args = [])
 * @method \Aws\Result createMultiRegionAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createMultiRegionAccessPointAsync(array $args = [])
 * @method \Aws\Result deleteAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointAsync(array $args = [])
 * @method \Aws\Result deleteAccessPointForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result deleteAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointPolicyAsync(array $args = [])
 * @method \Aws\Result deleteAccessPointPolicyForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointPolicyForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result deleteBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketAsync(array $args = [])
 * @method \Aws\Result deleteBucketLifecycleConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketLifecycleConfigurationAsync(array $args = [])
 * @method \Aws\Result deleteBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketPolicyAsync(array $args = [])
 * @method \Aws\Result deleteBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketReplicationAsync(array $args = [])
 * @method \Aws\Result deleteBucketTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketTaggingAsync(array $args = [])
 * @method \Aws\Result deleteJobTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteJobTaggingAsync(array $args = [])
 * @method \Aws\Result deleteMultiRegionAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteMultiRegionAccessPointAsync(array $args = [])
 * @method \Aws\Result deletePublicAccessBlock(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePublicAccessBlockAsync(array $args = [])
 * @method \Aws\Result deleteStorageLensConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteStorageLensConfigurationAsync(array $args = [])
 * @method \Aws\Result deleteStorageLensConfigurationTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteStorageLensConfigurationTaggingAsync(array $args = [])
 * @method \Aws\Result describeJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeJobAsync(array $args = [])
 * @method \Aws\Result describeMultiRegionAccessPointOperation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeMultiRegionAccessPointOperationAsync(array $args = [])
 * @method \Aws\Result getAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointAsync(array $args = [])
 * @method \Aws\Result getAccessPointConfigurationForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointConfigurationForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result getAccessPointForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result getAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointPolicyAsync(array $args = [])
 * @method \Aws\Result getAccessPointPolicyForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointPolicyForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result getAccessPointPolicyStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointPolicyStatusAsync(array $args = [])
 * @method \Aws\Result getAccessPointPolicyStatusForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointPolicyStatusForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result getBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketAsync(array $args = [])
 * @method \Aws\Result getBucketLifecycleConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketLifecycleConfigurationAsync(array $args = [])
 * @method \Aws\Result getBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketPolicyAsync(array $args = [])
 * @method \Aws\Result getBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketReplicationAsync(array $args = [])
 * @method \Aws\Result getBucketTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketTaggingAsync(array $args = [])
 * @method \Aws\Result getBucketVersioning(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketVersioningAsync(array $args = [])
 * @method \Aws\Result getJobTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getJobTaggingAsync(array $args = [])
 * @method \Aws\Result getMultiRegionAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMultiRegionAccessPointAsync(array $args = [])
 * @method \Aws\Result getMultiRegionAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMultiRegionAccessPointPolicyAsync(array $args = [])
 * @method \Aws\Result getMultiRegionAccessPointPolicyStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMultiRegionAccessPointPolicyStatusAsync(array $args = [])
 * @method \Aws\Result getMultiRegionAccessPointRoutes(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMultiRegionAccessPointRoutesAsync(array $args = [])
 * @method \Aws\Result getPublicAccessBlock(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getPublicAccessBlockAsync(array $args = [])
 * @method \Aws\Result getStorageLensConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getStorageLensConfigurationAsync(array $args = [])
 * @method \Aws\Result getStorageLensConfigurationTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getStorageLensConfigurationTaggingAsync(array $args = [])
 * @method \Aws\Result listAccessPoints(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccessPointsAsync(array $args = [])
 * @method \Aws\Result listAccessPointsForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccessPointsForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result listJobs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listJobsAsync(array $args = [])
 * @method \Aws\Result listMultiRegionAccessPoints(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listMultiRegionAccessPointsAsync(array $args = [])
 * @method \Aws\Result listRegionalBuckets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRegionalBucketsAsync(array $args = [])
 * @method \Aws\Result listStorageLensConfigurations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listStorageLensConfigurationsAsync(array $args = [])
 * @method \Aws\Result putAccessPointConfigurationForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAccessPointConfigurationForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result putAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAccessPointPolicyAsync(array $args = [])
 * @method \Aws\Result putAccessPointPolicyForObjectLambda(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAccessPointPolicyForObjectLambdaAsync(array $args = [])
 * @method \Aws\Result putBucketLifecycleConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketLifecycleConfigurationAsync(array $args = [])
 * @method \Aws\Result putBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketPolicyAsync(array $args = [])
 * @method \Aws\Result putBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketReplicationAsync(array $args = [])
 * @method \Aws\Result putBucketTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketTaggingAsync(array $args = [])
 * @method \Aws\Result putBucketVersioning(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketVersioningAsync(array $args = [])
 * @method \Aws\Result putJobTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putJobTaggingAsync(array $args = [])
 * @method \Aws\Result putMultiRegionAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putMultiRegionAccessPointPolicyAsync(array $args = [])
 * @method \Aws\Result putPublicAccessBlock(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPublicAccessBlockAsync(array $args = [])
 * @method \Aws\Result putStorageLensConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putStorageLensConfigurationAsync(array $args = [])
 * @method \Aws\Result putStorageLensConfigurationTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putStorageLensConfigurationTaggingAsync(array $args = [])
 * @method \Aws\Result submitMultiRegionAccessPointRoutes(array $args = [])
 * @method \GuzzleHttp\Promise\Promise submitMultiRegionAccessPointRoutesAsync(array $args = [])
 * @method \Aws\Result updateJobPriority(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateJobPriorityAsync(array $args = [])
 * @method \Aws\Result updateJobStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateJobStatusAsync(array $args = [])
 */
class S3ControlClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        return $args + ['use_dual_stack_endpoint' => ['type' => 'config', 'valid' => ['bool'], 'doc' => 'Set to true to send requests to an S3 Control Dual Stack' . ' endpoint by default, which enables IPv6 Protocol.' . ' Can be enabled or disabled on individual operations by setting' . ' \'@use_dual_stack_endpoint\' to true or false.', 'default' => \false], 'use_arn_region' => ['type' => 'config', 'valid' => ['bool', \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\Configuration::class, \WPStack_Connect_Vendor\Aws\CacheInterface::class, 'callable'], 'doc' => 'Set to true to allow passed in ARNs to override' . ' client region. Accepts...', 'fn' => [__CLASS__, '_apply_use_arn_region'], 'default' => [\WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationProvider::class, 'defaultProvider']]];
    }
    public static function _apply_use_arn_region($value, array &$args, \WPStack_Connect_Vendor\Aws\HandlerList $list)
    {
        if ($value instanceof \WPStack_Connect_Vendor\Aws\CacheInterface) {
            $value = \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationProvider::defaultProvider($args);
        }
        if (\is_callable($value)) {
            $value = $value();
        }
        if ($value instanceof \WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface) {
            $value = $value->wait();
        }
        if ($value instanceof \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationInterface) {
            $args['use_arn_region'] = $value;
        } else {
            // The Configuration class itself will validate other inputs
            $args['use_arn_region'] = new \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\Configuration($value);
        }
    }
    /**
     * {@inheritdoc}
     *
     * In addition to the options available to
     * {@see Aws\AwsClient::__construct}, S3ControlClient accepts the following
     * option:
     *
     * - use_dual_stack_endpoint: (bool) Set to true to send requests to an S3
     *   Control Dual Stack endpoint by default, which enables IPv6 Protocol.
     *   Can be enabled or disabled on individual operations by setting
     *   '@use_dual_stack_endpoint\' to true or false. Note:
     *   you cannot use it together with an accelerate endpoint.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        if ($this->isUseEndpointV2()) {
            $this->processEndpointV2Model();
        }
        $stack = $this->getHandlerList();
        $stack->appendBuild(\WPStack_Connect_Vendor\Aws\S3Control\EndpointArnMiddleware::wrap($this->getApi(), $this->getRegion(), ['use_arn_region' => $this->getConfig('use_arn_region'), 'dual_stack' => $this->getConfig('use_dual_stack_endpoint')->isUseDualStackEndpoint(), 'endpoint' => isset($args['endpoint']) ? $args['endpoint'] : null, 'use_fips_endpoint' => $this->getConfig('use_fips_endpoint')], $this->isUseEndpointV2()), 's3control.endpoint_arn_middleware');
    }
    /**
     * Modifies API definition to remove `AccountId`
     * host prefix.  This is now handled by the endpoint ruleset.
     *
     * @return void
     *
     * @internal
     */
    private function processEndpointV2Model()
    {
        $definition = $this->getApi()->getDefinition();
        $this->removeHostPrefix($definition);
        $this->removeRequiredMember($definition);
        $this->getApi()->setDefinition($definition);
    }
    private function removeHostPrefix(&$definition)
    {
        foreach ($definition['operations'] as &$operation) {
            if (isset($operation['endpoint']['hostPrefix']) && $operation['endpoint']['hostPrefix'] === '{AccountId}.') {
                $operation['endpoint']['hostPrefix'] = \str_replace('{AccountId}.', '', $operation['endpoint']['hostPrefix']);
            }
        }
    }
    private function removeRequiredMember(&$definition)
    {
        foreach ($definition['shapes'] as &$shape) {
            if (isset($shape['required'])) {
                $found = \array_search('AccountId', $shape['required']);
                if ($found !== \false) {
                    unset($shape['required'][$found]);
                }
            }
        }
    }
}
