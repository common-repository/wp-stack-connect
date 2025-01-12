<?php

namespace WPStack_Connect_Vendor\Aws\ElasticBeanstalk;

use WPStack_Connect_Vendor\Aws\AwsClient;
/**
 * This client is used to interact with the **AWS Elastic Beanstalk** service.
 *
 * @method \Aws\Result abortEnvironmentUpdate(array $args = [])
 * @method \GuzzleHttp\Promise\Promise abortEnvironmentUpdateAsync(array $args = [])
 * @method \Aws\Result applyEnvironmentManagedAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise applyEnvironmentManagedActionAsync(array $args = [])
 * @method \Aws\Result associateEnvironmentOperationsRole(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateEnvironmentOperationsRoleAsync(array $args = [])
 * @method \Aws\Result checkDNSAvailability(array $args = [])
 * @method \GuzzleHttp\Promise\Promise checkDNSAvailabilityAsync(array $args = [])
 * @method \Aws\Result composeEnvironments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise composeEnvironmentsAsync(array $args = [])
 * @method \Aws\Result createApplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createApplicationAsync(array $args = [])
 * @method \Aws\Result createApplicationVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createApplicationVersionAsync(array $args = [])
 * @method \Aws\Result createConfigurationTemplate(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createConfigurationTemplateAsync(array $args = [])
 * @method \Aws\Result createEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createEnvironmentAsync(array $args = [])
 * @method \Aws\Result createPlatformVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createPlatformVersionAsync(array $args = [])
 * @method \Aws\Result createStorageLocation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createStorageLocationAsync(array $args = [])
 * @method \Aws\Result deleteApplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteApplicationAsync(array $args = [])
 * @method \Aws\Result deleteApplicationVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteApplicationVersionAsync(array $args = [])
 * @method \Aws\Result deleteConfigurationTemplate(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteConfigurationTemplateAsync(array $args = [])
 * @method \Aws\Result deleteEnvironmentConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteEnvironmentConfigurationAsync(array $args = [])
 * @method \Aws\Result deletePlatformVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePlatformVersionAsync(array $args = [])
 * @method \Aws\Result describeAccountAttributes(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAccountAttributesAsync(array $args = [])
 * @method \Aws\Result describeApplicationVersions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeApplicationVersionsAsync(array $args = [])
 * @method \Aws\Result describeApplications(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeApplicationsAsync(array $args = [])
 * @method \Aws\Result describeConfigurationOptions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeConfigurationOptionsAsync(array $args = [])
 * @method \Aws\Result describeConfigurationSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeConfigurationSettingsAsync(array $args = [])
 * @method \Aws\Result describeEnvironmentHealth(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEnvironmentHealthAsync(array $args = [])
 * @method \Aws\Result describeEnvironmentManagedActionHistory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEnvironmentManagedActionHistoryAsync(array $args = [])
 * @method \Aws\Result describeEnvironmentManagedActions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEnvironmentManagedActionsAsync(array $args = [])
 * @method \Aws\Result describeEnvironmentResources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEnvironmentResourcesAsync(array $args = [])
 * @method \Aws\Result describeEnvironments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEnvironmentsAsync(array $args = [])
 * @method \Aws\Result describeEvents(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEventsAsync(array $args = [])
 * @method \Aws\Result describeInstancesHealth(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeInstancesHealthAsync(array $args = [])
 * @method \Aws\Result describePlatformVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePlatformVersionAsync(array $args = [])
 * @method \Aws\Result disassociateEnvironmentOperationsRole(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateEnvironmentOperationsRoleAsync(array $args = [])
 * @method \Aws\Result listAvailableSolutionStacks(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAvailableSolutionStacksAsync(array $args = [])
 * @method \Aws\Result listPlatformBranches(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPlatformBranchesAsync(array $args = [])
 * @method \Aws\Result listPlatformVersions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPlatformVersionsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result rebuildEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise rebuildEnvironmentAsync(array $args = [])
 * @method \Aws\Result requestEnvironmentInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise requestEnvironmentInfoAsync(array $args = [])
 * @method \Aws\Result restartAppServer(array $args = [])
 * @method \GuzzleHttp\Promise\Promise restartAppServerAsync(array $args = [])
 * @method \Aws\Result retrieveEnvironmentInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise retrieveEnvironmentInfoAsync(array $args = [])
 * @method \Aws\Result swapEnvironmentCNAMEs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise swapEnvironmentCNAMEsAsync(array $args = [])
 * @method \Aws\Result terminateEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise terminateEnvironmentAsync(array $args = [])
 * @method \Aws\Result updateApplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateApplicationAsync(array $args = [])
 * @method \Aws\Result updateApplicationResourceLifecycle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateApplicationResourceLifecycleAsync(array $args = [])
 * @method \Aws\Result updateApplicationVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateApplicationVersionAsync(array $args = [])
 * @method \Aws\Result updateConfigurationTemplate(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateConfigurationTemplateAsync(array $args = [])
 * @method \Aws\Result updateEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateEnvironmentAsync(array $args = [])
 * @method \Aws\Result updateTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateTagsForResourceAsync(array $args = [])
 * @method \Aws\Result validateConfigurationSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise validateConfigurationSettingsAsync(array $args = [])
 */
class ElasticBeanstalkClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
}
