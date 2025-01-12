<?php

namespace WPStack_Connect_Vendor\Aws\FMS;

use WPStack_Connect_Vendor\Aws\AwsClient;
/**
 * This client is used to interact with the **Firewall Management Service** service.
 * @method \Aws\Result associateAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateAdminAccountAsync(array $args = [])
 * @method \Aws\Result associateThirdPartyFirewall(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateThirdPartyFirewallAsync(array $args = [])
 * @method \Aws\Result batchAssociateResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchAssociateResourceAsync(array $args = [])
 * @method \Aws\Result batchDisassociateResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchDisassociateResourceAsync(array $args = [])
 * @method \Aws\Result deleteAppsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAppsListAsync(array $args = [])
 * @method \Aws\Result deleteNotificationChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteNotificationChannelAsync(array $args = [])
 * @method \Aws\Result deletePolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePolicyAsync(array $args = [])
 * @method \Aws\Result deleteProtocolsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProtocolsListAsync(array $args = [])
 * @method \Aws\Result deleteResourceSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteResourceSetAsync(array $args = [])
 * @method \Aws\Result disassociateAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateAdminAccountAsync(array $args = [])
 * @method \Aws\Result disassociateThirdPartyFirewall(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateThirdPartyFirewallAsync(array $args = [])
 * @method \Aws\Result getAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAdminAccountAsync(array $args = [])
 * @method \Aws\Result getAdminScope(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAdminScopeAsync(array $args = [])
 * @method \Aws\Result getAppsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAppsListAsync(array $args = [])
 * @method \Aws\Result getComplianceDetail(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getComplianceDetailAsync(array $args = [])
 * @method \Aws\Result getNotificationChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getNotificationChannelAsync(array $args = [])
 * @method \Aws\Result getPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getPolicyAsync(array $args = [])
 * @method \Aws\Result getProtectionStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getProtectionStatusAsync(array $args = [])
 * @method \Aws\Result getProtocolsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getProtocolsListAsync(array $args = [])
 * @method \Aws\Result getResourceSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getResourceSetAsync(array $args = [])
 * @method \Aws\Result getThirdPartyFirewallAssociationStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getThirdPartyFirewallAssociationStatusAsync(array $args = [])
 * @method \Aws\Result getViolationDetails(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getViolationDetailsAsync(array $args = [])
 * @method \Aws\Result listAdminAccountsForOrganization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAdminAccountsForOrganizationAsync(array $args = [])
 * @method \Aws\Result listAdminsManagingAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAdminsManagingAccountAsync(array $args = [])
 * @method \Aws\Result listAppsLists(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAppsListsAsync(array $args = [])
 * @method \Aws\Result listComplianceStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listComplianceStatusAsync(array $args = [])
 * @method \Aws\Result listDiscoveredResources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listDiscoveredResourcesAsync(array $args = [])
 * @method \Aws\Result listMemberAccounts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listMemberAccountsAsync(array $args = [])
 * @method \Aws\Result listPolicies(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPoliciesAsync(array $args = [])
 * @method \Aws\Result listProtocolsLists(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProtocolsListsAsync(array $args = [])
 * @method \Aws\Result listResourceSetResources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourceSetResourcesAsync(array $args = [])
 * @method \Aws\Result listResourceSets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourceSetsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result listThirdPartyFirewallFirewallPolicies(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listThirdPartyFirewallFirewallPoliciesAsync(array $args = [])
 * @method \Aws\Result putAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAdminAccountAsync(array $args = [])
 * @method \Aws\Result putAppsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAppsListAsync(array $args = [])
 * @method \Aws\Result putNotificationChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putNotificationChannelAsync(array $args = [])
 * @method \Aws\Result putPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPolicyAsync(array $args = [])
 * @method \Aws\Result putProtocolsList(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putProtocolsListAsync(array $args = [])
 * @method \Aws\Result putResourceSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putResourceSetAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 */
class FMSClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
}
