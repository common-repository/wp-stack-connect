<?php

namespace WPStack_Connect_Vendor;

// This file was auto-generated from sdk-root/src/data/license-manager-user-subscriptions/2018-05-10/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2018-05-10', 'endpointPrefix' => 'license-manager-user-subscriptions', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceFullName' => 'AWS License Manager User Subscriptions', 'serviceId' => 'License Manager User Subscriptions', 'signatureVersion' => 'v4', 'signingName' => 'license-manager-user-subscriptions', 'uid' => 'license-manager-user-subscriptions-2018-05-10'], 'operations' => ['AssociateUser' => ['name' => 'AssociateUser', 'http' => ['method' => 'POST', 'requestUri' => '/user/AssociateUser', 'responseCode' => 200], 'input' => ['shape' => 'AssociateUserRequest'], 'output' => ['shape' => 'AssociateUserResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true], 'DeregisterIdentityProvider' => ['name' => 'DeregisterIdentityProvider', 'http' => ['method' => 'POST', 'requestUri' => '/identity-provider/DeregisterIdentityProvider', 'responseCode' => 200], 'input' => ['shape' => 'DeregisterIdentityProviderRequest'], 'output' => ['shape' => 'DeregisterIdentityProviderResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true], 'DisassociateUser' => ['name' => 'DisassociateUser', 'http' => ['method' => 'POST', 'requestUri' => '/user/DisassociateUser', 'responseCode' => 200], 'input' => ['shape' => 'DisassociateUserRequest'], 'output' => ['shape' => 'DisassociateUserResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true], 'ListIdentityProviders' => ['name' => 'ListIdentityProviders', 'http' => ['method' => 'POST', 'requestUri' => '/identity-provider/ListIdentityProviders', 'responseCode' => 200], 'input' => ['shape' => 'ListIdentityProvidersRequest'], 'output' => ['shape' => 'ListIdentityProvidersResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'ListInstances' => ['name' => 'ListInstances', 'http' => ['method' => 'POST', 'requestUri' => '/instance/ListInstances', 'responseCode' => 200], 'input' => ['shape' => 'ListInstancesRequest'], 'output' => ['shape' => 'ListInstancesResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'ListProductSubscriptions' => ['name' => 'ListProductSubscriptions', 'http' => ['method' => 'POST', 'requestUri' => '/user/ListProductSubscriptions', 'responseCode' => 200], 'input' => ['shape' => 'ListProductSubscriptionsRequest'], 'output' => ['shape' => 'ListProductSubscriptionsResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'ListUserAssociations' => ['name' => 'ListUserAssociations', 'http' => ['method' => 'POST', 'requestUri' => '/user/ListUserAssociations', 'responseCode' => 200], 'input' => ['shape' => 'ListUserAssociationsRequest'], 'output' => ['shape' => 'ListUserAssociationsResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'RegisterIdentityProvider' => ['name' => 'RegisterIdentityProvider', 'http' => ['method' => 'POST', 'requestUri' => '/identity-provider/RegisterIdentityProvider', 'responseCode' => 200], 'input' => ['shape' => 'RegisterIdentityProviderRequest'], 'output' => ['shape' => 'RegisterIdentityProviderResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true], 'StartProductSubscription' => ['name' => 'StartProductSubscription', 'http' => ['method' => 'POST', 'requestUri' => '/user/StartProductSubscription', 'responseCode' => 200], 'input' => ['shape' => 'StartProductSubscriptionRequest'], 'output' => ['shape' => 'StartProductSubscriptionResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'StopProductSubscription' => ['name' => 'StopProductSubscription', 'http' => ['method' => 'POST', 'requestUri' => '/user/StopProductSubscription', 'responseCode' => 200], 'input' => ['shape' => 'StopProductSubscriptionRequest'], 'output' => ['shape' => 'StopProductSubscriptionResponse'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ConflictException'], ['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException']]], 'UpdateIdentityProviderSettings' => ['name' => 'UpdateIdentityProviderSettings', 'http' => ['method' => 'POST', 'requestUri' => '/identity-provider/UpdateIdentityProviderSettings', 'responseCode' => 200], 'input' => ['shape' => 'UpdateIdentityProviderSettingsRequest'], 'output' => ['shape' => 'UpdateIdentityProviderSettingsResponse'], 'errors' => [['shape' => 'ValidationException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true]], 'shapes' => ['AccessDeniedException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true], 'ActiveDirectoryIdentityProvider' => ['type' => 'structure', 'members' => ['DirectoryId' => ['shape' => 'String']]], 'AssociateUserRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'InstanceId', 'Username'], 'members' => ['Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'InstanceId' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'AssociateUserResponse' => ['type' => 'structure', 'required' => ['InstanceUserSummary'], 'members' => ['InstanceUserSummary' => ['shape' => 'InstanceUserSummary']]], 'BoxInteger' => ['type' => 'integer', 'box' => \true], 'ConflictException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true, 'fault' => \true], 'DeregisterIdentityProviderRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product'], 'members' => ['IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String']]], 'DeregisterIdentityProviderResponse' => ['type' => 'structure', 'required' => ['IdentityProviderSummary'], 'members' => ['IdentityProviderSummary' => ['shape' => 'IdentityProviderSummary']]], 'DisassociateUserRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'InstanceId', 'Username'], 'members' => ['Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'InstanceId' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'DisassociateUserResponse' => ['type' => 'structure', 'required' => ['InstanceUserSummary'], 'members' => ['InstanceUserSummary' => ['shape' => 'InstanceUserSummary']]], 'Filter' => ['type' => 'structure', 'members' => ['Attribute' => ['shape' => 'String'], 'Operation' => ['shape' => 'String'], 'Value' => ['shape' => 'String']]], 'FilterList' => ['type' => 'list', 'member' => ['shape' => 'Filter']], 'IdentityProvider' => ['type' => 'structure', 'members' => ['ActiveDirectoryIdentityProvider' => ['shape' => 'ActiveDirectoryIdentityProvider']], 'union' => \true], 'IdentityProviderSummary' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product', 'Settings', 'Status'], 'members' => ['FailureMessage' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'Settings' => ['shape' => 'Settings'], 'Status' => ['shape' => 'String']]], 'IdentityProviderSummaryList' => ['type' => 'list', 'member' => ['shape' => 'IdentityProviderSummary']], 'InstanceSummary' => ['type' => 'structure', 'required' => ['InstanceId', 'Products', 'Status'], 'members' => ['InstanceId' => ['shape' => 'String'], 'LastStatusCheckDate' => ['shape' => 'String'], 'Products' => ['shape' => 'StringList'], 'Status' => ['shape' => 'String'], 'StatusMessage' => ['shape' => 'String']]], 'InstanceSummaryList' => ['type' => 'list', 'member' => ['shape' => 'InstanceSummary']], 'InstanceUserSummary' => ['type' => 'structure', 'required' => ['IdentityProvider', 'InstanceId', 'Status', 'Username'], 'members' => ['AssociationDate' => ['shape' => 'String'], 'DisassociationDate' => ['shape' => 'String'], 'Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'InstanceId' => ['shape' => 'String'], 'Status' => ['shape' => 'String'], 'StatusMessage' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'InstanceUserSummaryList' => ['type' => 'list', 'member' => ['shape' => 'InstanceUserSummary']], 'InternalServerException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true, 'fault' => \true], 'ListIdentityProvidersRequest' => ['type' => 'structure', 'members' => ['MaxResults' => ['shape' => 'BoxInteger'], 'NextToken' => ['shape' => 'String']]], 'ListIdentityProvidersResponse' => ['type' => 'structure', 'required' => ['IdentityProviderSummaries'], 'members' => ['IdentityProviderSummaries' => ['shape' => 'IdentityProviderSummaryList'], 'NextToken' => ['shape' => 'String']]], 'ListInstancesRequest' => ['type' => 'structure', 'members' => ['Filters' => ['shape' => 'FilterList'], 'MaxResults' => ['shape' => 'BoxInteger'], 'NextToken' => ['shape' => 'String']]], 'ListInstancesResponse' => ['type' => 'structure', 'members' => ['InstanceSummaries' => ['shape' => 'InstanceSummaryList'], 'NextToken' => ['shape' => 'String']]], 'ListProductSubscriptionsRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product'], 'members' => ['Filters' => ['shape' => 'FilterList'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'MaxResults' => ['shape' => 'BoxInteger'], 'NextToken' => ['shape' => 'String'], 'Product' => ['shape' => 'String']]], 'ListProductSubscriptionsResponse' => ['type' => 'structure', 'members' => ['NextToken' => ['shape' => 'String'], 'ProductUserSummaries' => ['shape' => 'ProductUserSummaryList']]], 'ListUserAssociationsRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'InstanceId'], 'members' => ['Filters' => ['shape' => 'FilterList'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'InstanceId' => ['shape' => 'String'], 'MaxResults' => ['shape' => 'BoxInteger'], 'NextToken' => ['shape' => 'String']]], 'ListUserAssociationsResponse' => ['type' => 'structure', 'members' => ['InstanceUserSummaries' => ['shape' => 'InstanceUserSummaryList'], 'NextToken' => ['shape' => 'String']]], 'ProductUserSummary' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product', 'Status', 'Username'], 'members' => ['Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'Status' => ['shape' => 'String'], 'StatusMessage' => ['shape' => 'String'], 'SubscriptionEndDate' => ['shape' => 'String'], 'SubscriptionStartDate' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'ProductUserSummaryList' => ['type' => 'list', 'member' => ['shape' => 'ProductUserSummary']], 'RegisterIdentityProviderRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product'], 'members' => ['IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'Settings' => ['shape' => 'Settings']]], 'RegisterIdentityProviderResponse' => ['type' => 'structure', 'required' => ['IdentityProviderSummary'], 'members' => ['IdentityProviderSummary' => ['shape' => 'IdentityProviderSummary']]], 'ResourceNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 404, 'senderFault' => \true], 'exception' => \true], 'SecurityGroup' => ['type' => 'string', 'max' => 200, 'min' => 5, 'pattern' => '^sg-(([0-9a-z]{8})|([0-9a-z]{17}))$'], 'ServiceQuotaExceededException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true], 'Settings' => ['type' => 'structure', 'required' => ['SecurityGroupId', 'Subnets'], 'members' => ['SecurityGroupId' => ['shape' => 'SecurityGroup'], 'Subnets' => ['shape' => 'SettingsSubnetsList']]], 'SettingsSubnetsList' => ['type' => 'list', 'member' => ['shape' => 'Subnet'], 'min' => 1], 'StartProductSubscriptionRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product', 'Username'], 'members' => ['Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'StartProductSubscriptionResponse' => ['type' => 'structure', 'required' => ['ProductUserSummary'], 'members' => ['ProductUserSummary' => ['shape' => 'ProductUserSummary']]], 'StopProductSubscriptionRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product', 'Username'], 'members' => ['Domain' => ['shape' => 'String'], 'IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'Username' => ['shape' => 'String']]], 'StopProductSubscriptionResponse' => ['type' => 'structure', 'required' => ['ProductUserSummary'], 'members' => ['ProductUserSummary' => ['shape' => 'ProductUserSummary']]], 'String' => ['type' => 'string'], 'StringList' => ['type' => 'list', 'member' => ['shape' => 'String']], 'Subnet' => ['type' => 'string', 'pattern' => 'subnet-[a-z0-9]{8,17}'], 'Subnets' => ['type' => 'list', 'member' => ['shape' => 'Subnet']], 'ThrottlingException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true], 'UpdateIdentityProviderSettingsRequest' => ['type' => 'structure', 'required' => ['IdentityProvider', 'Product', 'UpdateSettings'], 'members' => ['IdentityProvider' => ['shape' => 'IdentityProvider'], 'Product' => ['shape' => 'String'], 'UpdateSettings' => ['shape' => 'UpdateSettings']]], 'UpdateIdentityProviderSettingsResponse' => ['type' => 'structure', 'required' => ['IdentityProviderSummary'], 'members' => ['IdentityProviderSummary' => ['shape' => 'IdentityProviderSummary']]], 'UpdateSettings' => ['type' => 'structure', 'required' => ['AddSubnets', 'RemoveSubnets'], 'members' => ['AddSubnets' => ['shape' => 'Subnets'], 'RemoveSubnets' => ['shape' => 'Subnets'], 'SecurityGroupId' => ['shape' => 'SecurityGroup']]], 'ValidationException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'exception' => \true]]];
