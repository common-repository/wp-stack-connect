<?php

namespace WPStack_Connect_Vendor;

// This file was auto-generated from sdk-root/src/data/codeguru-security/2018-05-10/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2018-05-10', 'endpointPrefix' => 'codeguru-security', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceFullName' => 'Amazon CodeGuru Security', 'serviceId' => 'CodeGuru Security', 'signatureVersion' => 'v4', 'signingName' => 'codeguru-security', 'uid' => 'codeguru-security-2018-05-10'], 'operations' => ['BatchGetFindings' => ['name' => 'BatchGetFindings', 'http' => ['method' => 'POST', 'requestUri' => '/batchGetFindings', 'responseCode' => 200], 'input' => ['shape' => 'BatchGetFindingsRequest'], 'output' => ['shape' => 'BatchGetFindingsResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'CreateScan' => ['name' => 'CreateScan', 'http' => ['method' => 'POST', 'requestUri' => '/scans', 'responseCode' => 200], 'input' => ['shape' => 'CreateScanRequest'], 'output' => ['shape' => 'CreateScanResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ConflictException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'CreateUploadUrl' => ['name' => 'CreateUploadUrl', 'http' => ['method' => 'POST', 'requestUri' => '/uploadUrl', 'responseCode' => 200], 'input' => ['shape' => 'CreateUploadUrlRequest'], 'output' => ['shape' => 'CreateUploadUrlResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'GetAccountConfiguration' => ['name' => 'GetAccountConfiguration', 'http' => ['method' => 'GET', 'requestUri' => '/accountConfiguration/get', 'responseCode' => 200], 'input' => ['shape' => 'GetAccountConfigurationRequest'], 'output' => ['shape' => 'GetAccountConfigurationResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'GetFindings' => ['name' => 'GetFindings', 'http' => ['method' => 'GET', 'requestUri' => '/findings/{scanName}', 'responseCode' => 200], 'input' => ['shape' => 'GetFindingsRequest'], 'output' => ['shape' => 'GetFindingsResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ConflictException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'GetMetricsSummary' => ['name' => 'GetMetricsSummary', 'http' => ['method' => 'GET', 'requestUri' => '/metrics/summary', 'responseCode' => 200], 'input' => ['shape' => 'GetMetricsSummaryRequest'], 'output' => ['shape' => 'GetMetricsSummaryResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'GetScan' => ['name' => 'GetScan', 'http' => ['method' => 'GET', 'requestUri' => '/scans/{scanName}', 'responseCode' => 200], 'input' => ['shape' => 'GetScanRequest'], 'output' => ['shape' => 'GetScanResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'AccessDeniedException']]], 'ListFindingsMetrics' => ['name' => 'ListFindingsMetrics', 'http' => ['method' => 'GET', 'requestUri' => '/metrics/findings', 'responseCode' => 200], 'input' => ['shape' => 'ListFindingsMetricsRequest'], 'output' => ['shape' => 'ListFindingsMetricsResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListScans' => ['name' => 'ListScans', 'http' => ['method' => 'GET', 'requestUri' => '/scans', 'responseCode' => 200], 'input' => ['shape' => 'ListScansRequest'], 'output' => ['shape' => 'ListScansResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListTagsForResource' => ['name' => 'ListTagsForResource', 'http' => ['method' => 'GET', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'ListTagsForResourceRequest'], 'output' => ['shape' => 'ListTagsForResourceResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ConflictException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'TagResource' => ['name' => 'TagResource', 'http' => ['method' => 'POST', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 204], 'input' => ['shape' => 'TagResourceRequest'], 'output' => ['shape' => 'TagResourceResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ConflictException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'UntagResource' => ['name' => 'UntagResource', 'http' => ['method' => 'DELETE', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 204], 'input' => ['shape' => 'UntagResourceRequest'], 'output' => ['shape' => 'UntagResourceResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ConflictException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']], 'idempotent' => \true], 'UpdateAccountConfiguration' => ['name' => 'UpdateAccountConfiguration', 'http' => ['method' => 'PUT', 'requestUri' => '/updateAccountConfiguration', 'responseCode' => 200], 'input' => ['shape' => 'UpdateAccountConfigurationRequest'], 'output' => ['shape' => 'UpdateAccountConfigurationResponse'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ThrottlingException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]]], 'shapes' => ['AccessDeniedException' => ['type' => 'structure', 'required' => ['errorCode', 'message'], 'members' => ['errorCode' => ['shape' => 'String'], 'message' => ['shape' => 'String'], 'resourceId' => ['shape' => 'String'], 'resourceType' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 403, 'senderFault' => \true], 'exception' => \true], 'AccountFindingsMetric' => ['type' => 'structure', 'members' => ['closedFindings' => ['shape' => 'FindingMetricsValuePerSeverity'], 'date' => ['shape' => 'Timestamp'], 'meanTimeToClose' => ['shape' => 'FindingMetricsValuePerSeverity'], 'newFindings' => ['shape' => 'FindingMetricsValuePerSeverity'], 'openFindings' => ['shape' => 'FindingMetricsValuePerSeverity']]], 'AnalysisType' => ['type' => 'string', 'enum' => ['Security', 'All']], 'BatchGetFindingsError' => ['type' => 'structure', 'required' => ['errorCode', 'findingId', 'message', 'scanName'], 'members' => ['errorCode' => ['shape' => 'ErrorCode'], 'findingId' => ['shape' => 'String'], 'message' => ['shape' => 'String'], 'scanName' => ['shape' => 'ScanName']]], 'BatchGetFindingsErrors' => ['type' => 'list', 'member' => ['shape' => 'BatchGetFindingsError']], 'BatchGetFindingsRequest' => ['type' => 'structure', 'required' => ['findingIdentifiers'], 'members' => ['findingIdentifiers' => ['shape' => 'FindingIdentifiers']]], 'BatchGetFindingsResponse' => ['type' => 'structure', 'required' => ['failedFindings', 'findings'], 'members' => ['failedFindings' => ['shape' => 'BatchGetFindingsErrors'], 'findings' => ['shape' => 'Findings']]], 'CategoriesWithMostFindings' => ['type' => 'list', 'member' => ['shape' => 'CategoryWithFindingNum'], 'max' => 5, 'min' => 0], 'CategoryWithFindingNum' => ['type' => 'structure', 'members' => ['categoryName' => ['shape' => 'String'], 'findingNumber' => ['shape' => 'Integer']]], 'ClientToken' => ['type' => 'string', 'max' => 64, 'min' => 1, 'pattern' => '^[\\S]+$'], 'CodeLine' => ['type' => 'structure', 'members' => ['content' => ['shape' => 'String'], 'number' => ['shape' => 'Integer']]], 'CodeSnippet' => ['type' => 'list', 'member' => ['shape' => 'CodeLine']], 'ConflictException' => ['type' => 'structure', 'required' => ['errorCode', 'message', 'resourceId', 'resourceType'], 'members' => ['errorCode' => ['shape' => 'String'], 'message' => ['shape' => 'String'], 'resourceId' => ['shape' => 'String'], 'resourceType' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 409, 'senderFault' => \true], 'exception' => \true], 'CreateScanRequest' => ['type' => 'structure', 'required' => ['resourceId', 'scanName'], 'members' => ['analysisType' => ['shape' => 'AnalysisType'], 'clientToken' => ['shape' => 'ClientToken', 'idempotencyToken' => \true], 'resourceId' => ['shape' => 'ResourceId'], 'scanName' => ['shape' => 'ScanName'], 'scanType' => ['shape' => 'ScanType'], 'tags' => ['shape' => 'TagMap']]], 'CreateScanResponse' => ['type' => 'structure', 'required' => ['resourceId', 'runId', 'scanName', 'scanState'], 'members' => ['resourceId' => ['shape' => 'ResourceId'], 'runId' => ['shape' => 'Uuid'], 'scanName' => ['shape' => 'ScanName'], 'scanNameArn' => ['shape' => 'ScanNameArn'], 'scanState' => ['shape' => 'ScanState']]], 'CreateUploadUrlRequest' => ['type' => 'structure', 'required' => ['scanName'], 'members' => ['scanName' => ['shape' => 'ScanName']]], 'CreateUploadUrlResponse' => ['type' => 'structure', 'required' => ['codeArtifactId', 'requestHeaders', 's3Url'], 'members' => ['codeArtifactId' => ['shape' => 'Uuid'], 'requestHeaders' => ['shape' => 'RequestHeaderMap'], 's3Url' => ['shape' => 'S3Url']]], 'DetectorTags' => ['type' => 'list', 'member' => ['shape' => 'String']], 'Double' => ['type' => 'double', 'box' => \true], 'EncryptionConfig' => ['type' => 'structure', 'members' => ['kmsKeyArn' => ['shape' => 'KmsKeyArn']]], 'ErrorCode' => ['type' => 'string', 'enum' => ['DUPLICATE_IDENTIFIER', 'ITEM_DOES_NOT_EXIST', 'INTERNAL_ERROR', 'INVALID_FINDING_ID', 'INVALID_SCAN_NAME']], 'FilePath' => ['type' => 'structure', 'members' => ['codeSnippet' => ['shape' => 'CodeSnippet'], 'endLine' => ['shape' => 'Integer'], 'name' => ['shape' => 'String'], 'path' => ['shape' => 'String'], 'startLine' => ['shape' => 'Integer']]], 'Finding' => ['type' => 'structure', 'members' => ['createdAt' => ['shape' => 'Timestamp'], 'description' => ['shape' => 'String'], 'detectorId' => ['shape' => 'String'], 'detectorName' => ['shape' => 'String'], 'detectorTags' => ['shape' => 'DetectorTags'], 'generatorId' => ['shape' => 'String'], 'id' => ['shape' => 'String'], 'remediation' => ['shape' => 'Remediation'], 'resource' => ['shape' => 'Resource'], 'ruleId' => ['shape' => 'String'], 'severity' => ['shape' => 'Severity'], 'status' => ['shape' => 'Status'], 'title' => ['shape' => 'String'], 'type' => ['shape' => 'String'], 'updatedAt' => ['shape' => 'Timestamp'], 'vulnerability' => ['shape' => 'Vulnerability']]], 'FindingIdentifier' => ['type' => 'structure', 'required' => ['findingId', 'scanName'], 'members' => ['findingId' => ['shape' => 'String'], 'scanName' => ['shape' => 'String']]], 'FindingIdentifiers' => ['type' => 'list', 'member' => ['shape' => 'FindingIdentifier'], 'max' => 25, 'min' => 1], 'FindingMetricsValuePerSeverity' => ['type' => 'structure', 'members' => ['critical' => ['shape' => 'Double'], 'high' => ['shape' => 'Double'], 'info' => ['shape' => 'Double'], 'low' => ['shape' => 'Double'], 'medium' => ['shape' => 'Double']]], 'Findings' => ['type' => 'list', 'member' => ['shape' => 'Finding']], 'FindingsMetricList' => ['type' => 'list', 'member' => ['shape' => 'AccountFindingsMetric']], 'GetAccountConfigurationRequest' => ['type' => 'structure', 'members' => []], 'GetAccountConfigurationResponse' => ['type' => 'structure', 'required' => ['encryptionConfig'], 'members' => ['encryptionConfig' => ['shape' => 'EncryptionConfig']]], 'GetFindingsRequest' => ['type' => 'structure', 'required' => ['scanName'], 'members' => ['maxResults' => ['shape' => 'GetFindingsRequestMaxResultsInteger', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken'], 'scanName' => ['shape' => 'ScanName', 'location' => 'uri', 'locationName' => 'scanName'], 'status' => ['shape' => 'Status', 'location' => 'querystring', 'locationName' => 'status']]], 'GetFindingsRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'GetFindingsResponse' => ['type' => 'structure', 'members' => ['findings' => ['shape' => 'Findings'], 'nextToken' => ['shape' => 'NextToken']]], 'GetMetricsSummaryRequest' => ['type' => 'structure', 'required' => ['date'], 'members' => ['date' => ['shape' => 'Timestamp', 'location' => 'querystring', 'locationName' => 'date']]], 'GetMetricsSummaryResponse' => ['type' => 'structure', 'members' => ['metricsSummary' => ['shape' => 'MetricsSummary']]], 'GetScanRequest' => ['type' => 'structure', 'required' => ['scanName'], 'members' => ['runId' => ['shape' => 'Uuid', 'location' => 'querystring', 'locationName' => 'runId'], 'scanName' => ['shape' => 'ScanName', 'location' => 'uri', 'locationName' => 'scanName']]], 'GetScanResponse' => ['type' => 'structure', 'required' => ['analysisType', 'createdAt', 'runId', 'scanName', 'scanState'], 'members' => ['analysisType' => ['shape' => 'AnalysisType'], 'createdAt' => ['shape' => 'Timestamp'], 'numberOfRevisions' => ['shape' => 'Long'], 'runId' => ['shape' => 'Uuid'], 'scanName' => ['shape' => 'ScanName'], 'scanNameArn' => ['shape' => 'ScanNameArn'], 'scanState' => ['shape' => 'ScanState'], 'updatedAt' => ['shape' => 'Timestamp']]], 'HeaderKey' => ['type' => 'string', 'min' => 1], 'HeaderValue' => ['type' => 'string', 'min' => 1], 'Integer' => ['type' => 'integer', 'box' => \true], 'InternalServerException' => ['type' => 'structure', 'members' => ['error' => ['shape' => 'String'], 'message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 500], 'exception' => \true, 'fault' => \true, 'retryable' => ['throttling' => \false]], 'KmsKeyArn' => ['type' => 'string', 'max' => 2048, 'min' => 1, 'pattern' => '^arn:aws:kms:[\\S]+:[\\d]{12}:key\\/(([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})|(mrk-[0-9a-zA-Z]{32}))$'], 'ListFindingsMetricsRequest' => ['type' => 'structure', 'required' => ['endDate', 'startDate'], 'members' => ['endDate' => ['shape' => 'Timestamp', 'location' => 'querystring', 'locationName' => 'endDate'], 'maxResults' => ['shape' => 'ListFindingsMetricsRequestMaxResultsInteger', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken'], 'startDate' => ['shape' => 'Timestamp', 'location' => 'querystring', 'locationName' => 'startDate']]], 'ListFindingsMetricsRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 1000, 'min' => 1], 'ListFindingsMetricsResponse' => ['type' => 'structure', 'members' => ['findingsMetrics' => ['shape' => 'FindingsMetricList'], 'nextToken' => ['shape' => 'NextToken']]], 'ListScansRequest' => ['type' => 'structure', 'members' => ['maxResults' => ['shape' => 'ListScansRequestMaxResultsInteger', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListScansRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'ListScansResponse' => ['type' => 'structure', 'members' => ['nextToken' => ['shape' => 'NextToken'], 'summaries' => ['shape' => 'ScanSummaries']]], 'ListTagsForResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn'], 'members' => ['resourceArn' => ['shape' => 'ScanNameArn', 'location' => 'uri', 'locationName' => 'resourceArn']]], 'ListTagsForResourceResponse' => ['type' => 'structure', 'members' => ['tags' => ['shape' => 'TagMap']]], 'Long' => ['type' => 'long', 'box' => \true], 'MetricsSummary' => ['type' => 'structure', 'members' => ['categoriesWithMostFindings' => ['shape' => 'CategoriesWithMostFindings'], 'date' => ['shape' => 'Timestamp'], 'openFindings' => ['shape' => 'FindingMetricsValuePerSeverity'], 'scansWithMostOpenCriticalFindings' => ['shape' => 'ScansWithMostOpenCriticalFindings'], 'scansWithMostOpenFindings' => ['shape' => 'ScansWithMostOpenFindings']]], 'NextToken' => ['type' => 'string', 'max' => 2048, 'min' => 1, 'pattern' => '^[\\S]+$'], 'Recommendation' => ['type' => 'structure', 'members' => ['text' => ['shape' => 'String'], 'url' => ['shape' => 'String']]], 'ReferenceUrls' => ['type' => 'list', 'member' => ['shape' => 'String']], 'RelatedVulnerabilities' => ['type' => 'list', 'member' => ['shape' => 'String']], 'Remediation' => ['type' => 'structure', 'members' => ['recommendation' => ['shape' => 'Recommendation'], 'suggestedFixes' => ['shape' => 'SuggestedFixes']]], 'RequestHeaderMap' => ['type' => 'map', 'key' => ['shape' => 'HeaderKey'], 'value' => ['shape' => 'HeaderValue'], 'sensitive' => \true], 'Resource' => ['type' => 'structure', 'members' => ['id' => ['shape' => 'String'], 'subResourceId' => ['shape' => 'String']]], 'ResourceId' => ['type' => 'structure', 'members' => ['codeArtifactId' => ['shape' => 'Uuid']], 'union' => \true], 'ResourceNotFoundException' => ['type' => 'structure', 'required' => ['errorCode', 'message', 'resourceId', 'resourceType'], 'members' => ['errorCode' => ['shape' => 'String'], 'message' => ['shape' => 'String'], 'resourceId' => ['shape' => 'String'], 'resourceType' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 404, 'senderFault' => \true], 'exception' => \true], 'S3Url' => ['type' => 'string', 'min' => 1, 'sensitive' => \true], 'ScanName' => ['type' => 'string', 'max' => 140, 'min' => 1, 'pattern' => '^[a-zA-Z0-9-_$:.]*$'], 'ScanNameArn' => ['type' => 'string', 'max' => 300, 'min' => 1, 'pattern' => '^arn:aws:codeguru-security:[\\S]+:[\\d]{12}:scans\\/[a-zA-Z0-9-_$:.]*$'], 'ScanNameWithFindingNum' => ['type' => 'structure', 'members' => ['findingNumber' => ['shape' => 'Integer'], 'scanName' => ['shape' => 'String']]], 'ScanState' => ['type' => 'string', 'enum' => ['InProgress', 'Successful', 'Failed']], 'ScanSummaries' => ['type' => 'list', 'member' => ['shape' => 'ScanSummary']], 'ScanSummary' => ['type' => 'structure', 'required' => ['createdAt', 'runId', 'scanName', 'scanState'], 'members' => ['createdAt' => ['shape' => 'Timestamp'], 'runId' => ['shape' => 'Uuid'], 'scanName' => ['shape' => 'ScanName'], 'scanNameArn' => ['shape' => 'ScanNameArn'], 'scanState' => ['shape' => 'ScanState'], 'updatedAt' => ['shape' => 'Timestamp']]], 'ScanType' => ['type' => 'string', 'enum' => ['Standard', 'Express']], 'ScansWithMostOpenCriticalFindings' => ['type' => 'list', 'member' => ['shape' => 'ScanNameWithFindingNum'], 'max' => 3, 'min' => 0], 'ScansWithMostOpenFindings' => ['type' => 'list', 'member' => ['shape' => 'ScanNameWithFindingNum'], 'max' => 3, 'min' => 0], 'Severity' => ['type' => 'string', 'enum' => ['Critical', 'High', 'Medium', 'Low', 'Info']], 'Status' => ['type' => 'string', 'enum' => ['Closed', 'Open', 'All']], 'String' => ['type' => 'string'], 'SuggestedFix' => ['type' => 'structure', 'members' => ['code' => ['shape' => 'String'], 'description' => ['shape' => 'String']]], 'SuggestedFixes' => ['type' => 'list', 'member' => ['shape' => 'SuggestedFix']], 'TagKey' => ['type' => 'string', 'max' => 128, 'min' => 1], 'TagKeyList' => ['type' => 'list', 'member' => ['shape' => 'TagKey'], 'max' => 200, 'min' => 0], 'TagMap' => ['type' => 'map', 'key' => ['shape' => 'TagKey'], 'value' => ['shape' => 'TagValue'], 'max' => 200, 'min' => 0], 'TagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tags'], 'members' => ['resourceArn' => ['shape' => 'ScanNameArn', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tags' => ['shape' => 'TagMap']]], 'TagResourceResponse' => ['type' => 'structure', 'members' => []], 'TagValue' => ['type' => 'string', 'max' => 256, 'min' => 0], 'ThrottlingException' => ['type' => 'structure', 'required' => ['errorCode', 'message'], 'members' => ['errorCode' => ['shape' => 'String'], 'message' => ['shape' => 'String'], 'quotaCode' => ['shape' => 'String'], 'serviceCode' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 429, 'senderFault' => \true], 'exception' => \true, 'retryable' => ['throttling' => \true]], 'Timestamp' => ['type' => 'timestamp'], 'UntagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tagKeys'], 'members' => ['resourceArn' => ['shape' => 'ScanNameArn', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tagKeys' => ['shape' => 'TagKeyList', 'location' => 'querystring', 'locationName' => 'tagKeys']]], 'UntagResourceResponse' => ['type' => 'structure', 'members' => []], 'UpdateAccountConfigurationRequest' => ['type' => 'structure', 'required' => ['encryptionConfig'], 'members' => ['encryptionConfig' => ['shape' => 'EncryptionConfig']]], 'UpdateAccountConfigurationResponse' => ['type' => 'structure', 'required' => ['encryptionConfig'], 'members' => ['encryptionConfig' => ['shape' => 'EncryptionConfig']]], 'Uuid' => ['type' => 'string', 'pattern' => '^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$'], 'ValidationException' => ['type' => 'structure', 'required' => ['errorCode', 'message', 'reason'], 'members' => ['errorCode' => ['shape' => 'String'], 'fieldList' => ['shape' => 'ValidationExceptionFieldList'], 'message' => ['shape' => 'String'], 'reason' => ['shape' => 'ValidationExceptionReason']], 'error' => ['httpStatusCode' => 400, 'senderFault' => \true], 'exception' => \true], 'ValidationExceptionField' => ['type' => 'structure', 'required' => ['message', 'name'], 'members' => ['message' => ['shape' => 'String'], 'name' => ['shape' => 'String']]], 'ValidationExceptionFieldList' => ['type' => 'list', 'member' => ['shape' => 'ValidationExceptionField']], 'ValidationExceptionReason' => ['type' => 'string', 'enum' => ['unknownOperation', 'cannotParse', 'fieldValidationFailed', 'other', 'lambdaCodeShaMisMatch']], 'Vulnerability' => ['type' => 'structure', 'members' => ['filePath' => ['shape' => 'FilePath'], 'id' => ['shape' => 'String'], 'itemCount' => ['shape' => 'Integer'], 'referenceUrls' => ['shape' => 'ReferenceUrls'], 'relatedVulnerabilities' => ['shape' => 'RelatedVulnerabilities']]]]];
