<?php

namespace WPStack_Connect_Vendor\Aws\SSOAdmin;

use WPStack_Connect_Vendor\Aws\AwsClient;
/**
 * This client is used to interact with the **AWS Single Sign-On Admin** service.
 * @method \Aws\Result attachCustomerManagedPolicyReferenceToPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise attachCustomerManagedPolicyReferenceToPermissionSetAsync(array $args = [])
 * @method \Aws\Result attachManagedPolicyToPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise attachManagedPolicyToPermissionSetAsync(array $args = [])
 * @method \Aws\Result createAccountAssignment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAccountAssignmentAsync(array $args = [])
 * @method \Aws\Result createInstanceAccessControlAttributeConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createInstanceAccessControlAttributeConfigurationAsync(array $args = [])
 * @method \Aws\Result createPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createPermissionSetAsync(array $args = [])
 * @method \Aws\Result deleteAccountAssignment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccountAssignmentAsync(array $args = [])
 * @method \Aws\Result deleteInlinePolicyFromPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteInlinePolicyFromPermissionSetAsync(array $args = [])
 * @method \Aws\Result deleteInstanceAccessControlAttributeConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteInstanceAccessControlAttributeConfigurationAsync(array $args = [])
 * @method \Aws\Result deletePermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePermissionSetAsync(array $args = [])
 * @method \Aws\Result deletePermissionsBoundaryFromPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePermissionsBoundaryFromPermissionSetAsync(array $args = [])
 * @method \Aws\Result describeAccountAssignmentCreationStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAccountAssignmentCreationStatusAsync(array $args = [])
 * @method \Aws\Result describeAccountAssignmentDeletionStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAccountAssignmentDeletionStatusAsync(array $args = [])
 * @method \Aws\Result describeInstanceAccessControlAttributeConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeInstanceAccessControlAttributeConfigurationAsync(array $args = [])
 * @method \Aws\Result describePermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePermissionSetAsync(array $args = [])
 * @method \Aws\Result describePermissionSetProvisioningStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePermissionSetProvisioningStatusAsync(array $args = [])
 * @method \Aws\Result detachCustomerManagedPolicyReferenceFromPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise detachCustomerManagedPolicyReferenceFromPermissionSetAsync(array $args = [])
 * @method \Aws\Result detachManagedPolicyFromPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise detachManagedPolicyFromPermissionSetAsync(array $args = [])
 * @method \Aws\Result getInlinePolicyForPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getInlinePolicyForPermissionSetAsync(array $args = [])
 * @method \Aws\Result getPermissionsBoundaryForPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getPermissionsBoundaryForPermissionSetAsync(array $args = [])
 * @method \Aws\Result listAccountAssignmentCreationStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccountAssignmentCreationStatusAsync(array $args = [])
 * @method \Aws\Result listAccountAssignmentDeletionStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccountAssignmentDeletionStatusAsync(array $args = [])
 * @method \Aws\Result listAccountAssignments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccountAssignmentsAsync(array $args = [])
 * @method \Aws\Result listAccountsForProvisionedPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccountsForProvisionedPermissionSetAsync(array $args = [])
 * @method \Aws\Result listCustomerManagedPolicyReferencesInPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCustomerManagedPolicyReferencesInPermissionSetAsync(array $args = [])
 * @method \Aws\Result listInstances(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listInstancesAsync(array $args = [])
 * @method \Aws\Result listManagedPoliciesInPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listManagedPoliciesInPermissionSetAsync(array $args = [])
 * @method \Aws\Result listPermissionSetProvisioningStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPermissionSetProvisioningStatusAsync(array $args = [])
 * @method \Aws\Result listPermissionSets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPermissionSetsAsync(array $args = [])
 * @method \Aws\Result listPermissionSetsProvisionedToAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPermissionSetsProvisionedToAccountAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result provisionPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise provisionPermissionSetAsync(array $args = [])
 * @method \Aws\Result putInlinePolicyToPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putInlinePolicyToPermissionSetAsync(array $args = [])
 * @method \Aws\Result putPermissionsBoundaryToPermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPermissionsBoundaryToPermissionSetAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateInstanceAccessControlAttributeConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateInstanceAccessControlAttributeConfigurationAsync(array $args = [])
 * @method \Aws\Result updatePermissionSet(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updatePermissionSetAsync(array $args = [])
 */
class SSOAdminClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
}
