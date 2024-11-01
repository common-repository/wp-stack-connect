<?php

namespace WPStack_Connect_Vendor;

// This file was auto-generated from sdk-root/src/data/braket/2019-09-01/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2019-09-01', 'endpointPrefix' => 'braket', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceFullName' => 'Braket', 'serviceId' => 'Braket', 'signatureVersion' => 'v4', 'signingName' => 'braket', 'uid' => 'braket-2019-09-01'], 'operations' => ['CancelJob' => ['name' => 'CancelJob', 'http' => ['method' => 'PUT', 'requestUri' => '/job/{jobArn}/cancel', 'responseCode' => 200], 'input' => ['shape' => 'CancelJobRequest'], 'output' => ['shape' => 'CancelJobResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ConflictException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']], 'idempotent' => \true], 'CancelQuantumTask' => ['name' => 'CancelQuantumTask', 'http' => ['method' => 'PUT', 'requestUri' => '/quantum-task/{quantumTaskArn}/cancel', 'responseCode' => 200], 'input' => ['shape' => 'CancelQuantumTaskRequest'], 'output' => ['shape' => 'CancelQuantumTaskResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ConflictException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']], 'idempotent' => \true], 'CreateJob' => ['name' => 'CreateJob', 'http' => ['method' => 'POST', 'requestUri' => '/job', 'responseCode' => 201], 'input' => ['shape' => 'CreateJobRequest'], 'output' => ['shape' => 'CreateJobResponse'], 'errors' => [['shape' => 'ConflictException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'DeviceRetiredException'], ['shape' => 'InternalServiceException'], ['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ValidationException']]], 'CreateQuantumTask' => ['name' => 'CreateQuantumTask', 'http' => ['method' => 'POST', 'requestUri' => '/quantum-task', 'responseCode' => 201], 'input' => ['shape' => 'CreateQuantumTaskRequest'], 'output' => ['shape' => 'CreateQuantumTaskResponse'], 'errors' => [['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'DeviceOfflineException'], ['shape' => 'DeviceRetiredException'], ['shape' => 'InternalServiceException'], ['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ValidationException']]], 'GetDevice' => ['name' => 'GetDevice', 'http' => ['method' => 'GET', 'requestUri' => '/device/{deviceArn}', 'responseCode' => 200], 'input' => ['shape' => 'GetDeviceRequest'], 'output' => ['shape' => 'GetDeviceResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'GetJob' => ['name' => 'GetJob', 'http' => ['method' => 'GET', 'requestUri' => '/job/{jobArn}', 'responseCode' => 200], 'input' => ['shape' => 'GetJobRequest'], 'output' => ['shape' => 'GetJobResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'GetQuantumTask' => ['name' => 'GetQuantumTask', 'http' => ['method' => 'GET', 'requestUri' => '/quantum-task/{quantumTaskArn}', 'responseCode' => 200], 'input' => ['shape' => 'GetQuantumTaskRequest'], 'output' => ['shape' => 'GetQuantumTaskResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'ListTagsForResource' => ['name' => 'ListTagsForResource', 'http' => ['method' => 'GET', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'ListTagsForResourceRequest'], 'output' => ['shape' => 'ListTagsForResourceResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'SearchDevices' => ['name' => 'SearchDevices', 'http' => ['method' => 'POST', 'requestUri' => '/devices', 'responseCode' => 200], 'input' => ['shape' => 'SearchDevicesRequest'], 'output' => ['shape' => 'SearchDevicesResponse'], 'errors' => [['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'SearchJobs' => ['name' => 'SearchJobs', 'http' => ['method' => 'POST', 'requestUri' => '/jobs', 'responseCode' => 200], 'input' => ['shape' => 'SearchJobsRequest'], 'output' => ['shape' => 'SearchJobsResponse'], 'errors' => [['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'SearchQuantumTasks' => ['name' => 'SearchQuantumTasks', 'http' => ['method' => 'POST', 'requestUri' => '/quantum-tasks', 'responseCode' => 200], 'input' => ['shape' => 'SearchQuantumTasksRequest'], 'output' => ['shape' => 'SearchQuantumTasksResponse'], 'errors' => [['shape' => 'AccessDeniedException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'TagResource' => ['name' => 'TagResource', 'http' => ['method' => 'POST', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'TagResourceRequest'], 'output' => ['shape' => 'TagResourceResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']]], 'UntagResource' => ['name' => 'UntagResource', 'http' => ['method' => 'DELETE', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'UntagResourceRequest'], 'output' => ['shape' => 'UntagResourceResponse'], 'errors' => [['shape' => 'ResourceNotFoundException'], ['shape' => 'InternalServiceException'], ['shape' => 'ValidationException']], 'idempotent' => \true]], 'shapes' => ['AccessDeniedException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 403, 'senderFault' => \true], 'exception' => \true], 'AlgorithmSpecification' => ['type' => 'structure', 'members' => ['containerImage' => ['shape' => 'ContainerImage'], 'scriptModeConfig' => ['shape' => 'ScriptModeConfig']]], 'CancelJobRequest' => ['type' => 'structure', 'required' => ['jobArn'], 'members' => ['jobArn' => ['shape' => 'JobArn', 'location' => 'uri', 'locationName' => 'jobArn']]], 'CancelJobResponse' => ['type' => 'structure', 'required' => ['cancellationStatus', 'jobArn'], 'members' => ['cancellationStatus' => ['shape' => 'CancellationStatus'], 'jobArn' => ['shape' => 'JobArn']]], 'CancelQuantumTaskRequest' => ['type' => 'structure', 'required' => ['clientToken', 'quantumTaskArn'], 'members' => ['clientToken' => ['shape' => 'String64', 'idempotencyToken' => \true], 'quantumTaskArn' => ['shape' => 'QuantumTaskArn', 'location' => 'uri', 'locationName' => 'quantumTaskArn']]], 'CancelQuantumTaskResponse' => ['type' => 'structure', 'required' => ['cancellationStatus', 'quantumTaskArn'], 'members' => ['cancellationStatus' => ['shape' => 'CancellationStatus'], 'quantumTaskArn' => ['shape' => 'QuantumTaskArn']]], 'CancellationStatus' => ['type' => 'string', 'enum' => ['CANCELLING', 'CANCELLED']], 'CompressionType' => ['type' => 'string', 'enum' => ['NONE', 'GZIP']], 'ConflictException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 409, 'senderFault' => \true], 'exception' => \true], 'ContainerImage' => ['type' => 'structure', 'required' => ['uri'], 'members' => ['uri' => ['shape' => 'Uri']]], 'CreateJobRequest' => ['type' => 'structure', 'required' => ['algorithmSpecification', 'clientToken', 'deviceConfig', 'instanceConfig', 'jobName', 'outputDataConfig', 'roleArn'], 'members' => ['algorithmSpecification' => ['shape' => 'AlgorithmSpecification'], 'checkpointConfig' => ['shape' => 'JobCheckpointConfig'], 'clientToken' => ['shape' => 'String64', 'idempotencyToken' => \true], 'deviceConfig' => ['shape' => 'DeviceConfig'], 'hyperParameters' => ['shape' => 'HyperParameters'], 'inputDataConfig' => ['shape' => 'CreateJobRequestInputDataConfigList'], 'instanceConfig' => ['shape' => 'InstanceConfig'], 'jobName' => ['shape' => 'CreateJobRequestJobNameString'], 'outputDataConfig' => ['shape' => 'JobOutputDataConfig'], 'roleArn' => ['shape' => 'RoleArn'], 'stoppingCondition' => ['shape' => 'JobStoppingCondition'], 'tags' => ['shape' => 'TagsMap']]], 'CreateJobRequestInputDataConfigList' => ['type' => 'list', 'member' => ['shape' => 'InputFileConfig'], 'max' => 20, 'min' => 0], 'CreateJobRequestJobNameString' => ['type' => 'string', 'max' => 50, 'min' => 1, 'pattern' => '^[a-zA-Z0-9](-*[a-zA-Z0-9]){0,50}$'], 'CreateJobResponse' => ['type' => 'structure', 'required' => ['jobArn'], 'members' => ['jobArn' => ['shape' => 'JobArn']]], 'CreateQuantumTaskRequest' => ['type' => 'structure', 'required' => ['action', 'clientToken', 'deviceArn', 'outputS3Bucket', 'outputS3KeyPrefix', 'shots'], 'members' => ['action' => ['shape' => 'JsonValue', 'jsonvalue' => \true], 'clientToken' => ['shape' => 'String64', 'idempotencyToken' => \true], 'deviceArn' => ['shape' => 'DeviceArn'], 'deviceParameters' => ['shape' => 'CreateQuantumTaskRequestDeviceParametersString', 'jsonvalue' => \true], 'jobToken' => ['shape' => 'JobToken'], 'outputS3Bucket' => ['shape' => 'CreateQuantumTaskRequestOutputS3BucketString'], 'outputS3KeyPrefix' => ['shape' => 'CreateQuantumTaskRequestOutputS3KeyPrefixString'], 'shots' => ['shape' => 'CreateQuantumTaskRequestShotsLong'], 'tags' => ['shape' => 'TagsMap']]], 'CreateQuantumTaskRequestDeviceParametersString' => ['type' => 'string', 'max' => 48000, 'min' => 1], 'CreateQuantumTaskRequestOutputS3BucketString' => ['type' => 'string', 'max' => 63, 'min' => 3], 'CreateQuantumTaskRequestOutputS3KeyPrefixString' => ['type' => 'string', 'max' => 1024, 'min' => 1], 'CreateQuantumTaskRequestShotsLong' => ['type' => 'long', 'box' => \true, 'min' => 0], 'CreateQuantumTaskResponse' => ['type' => 'structure', 'required' => ['quantumTaskArn'], 'members' => ['quantumTaskArn' => ['shape' => 'QuantumTaskArn']]], 'DataSource' => ['type' => 'structure', 'required' => ['s3DataSource'], 'members' => ['s3DataSource' => ['shape' => 'S3DataSource']]], 'DeviceArn' => ['type' => 'string', 'max' => 256, 'min' => 1], 'DeviceConfig' => ['type' => 'structure', 'required' => ['device'], 'members' => ['device' => ['shape' => 'String256']]], 'DeviceOfflineException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 424, 'senderFault' => \true], 'exception' => \true], 'DeviceRetiredException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 410, 'senderFault' => \true], 'exception' => \true], 'DeviceStatus' => ['type' => 'string', 'enum' => ['ONLINE', 'OFFLINE', 'RETIRED']], 'DeviceSummary' => ['type' => 'structure', 'required' => ['deviceArn', 'deviceName', 'deviceStatus', 'deviceType', 'providerName'], 'members' => ['deviceArn' => ['shape' => 'DeviceArn'], 'deviceName' => ['shape' => 'String'], 'deviceStatus' => ['shape' => 'DeviceStatus'], 'deviceType' => ['shape' => 'DeviceType'], 'providerName' => ['shape' => 'String']]], 'DeviceSummaryList' => ['type' => 'list', 'member' => ['shape' => 'DeviceSummary']], 'DeviceType' => ['type' => 'string', 'enum' => ['QPU', 'SIMULATOR']], 'GetDeviceRequest' => ['type' => 'structure', 'required' => ['deviceArn'], 'members' => ['deviceArn' => ['shape' => 'DeviceArn', 'location' => 'uri', 'locationName' => 'deviceArn']]], 'GetDeviceResponse' => ['type' => 'structure', 'required' => ['deviceArn', 'deviceCapabilities', 'deviceName', 'deviceStatus', 'deviceType', 'providerName'], 'members' => ['deviceArn' => ['shape' => 'DeviceArn'], 'deviceCapabilities' => ['shape' => 'JsonValue', 'jsonvalue' => \true], 'deviceName' => ['shape' => 'String'], 'deviceStatus' => ['shape' => 'DeviceStatus'], 'deviceType' => ['shape' => 'DeviceType'], 'providerName' => ['shape' => 'String']]], 'GetJobRequest' => ['type' => 'structure', 'required' => ['jobArn'], 'members' => ['jobArn' => ['shape' => 'JobArn', 'location' => 'uri', 'locationName' => 'jobArn']]], 'GetJobResponse' => ['type' => 'structure', 'required' => ['algorithmSpecification', 'createdAt', 'instanceConfig', 'jobArn', 'jobName', 'outputDataConfig', 'roleArn', 'status'], 'members' => ['algorithmSpecification' => ['shape' => 'AlgorithmSpecification'], 'billableDuration' => ['shape' => 'Integer'], 'checkpointConfig' => ['shape' => 'JobCheckpointConfig'], 'createdAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'deviceConfig' => ['shape' => 'DeviceConfig'], 'endedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'events' => ['shape' => 'JobEvents'], 'failureReason' => ['shape' => 'String1024'], 'hyperParameters' => ['shape' => 'HyperParameters'], 'inputDataConfig' => ['shape' => 'InputConfigList'], 'instanceConfig' => ['shape' => 'InstanceConfig'], 'jobArn' => ['shape' => 'JobArn'], 'jobName' => ['shape' => 'GetJobResponseJobNameString'], 'outputDataConfig' => ['shape' => 'JobOutputDataConfig'], 'roleArn' => ['shape' => 'RoleArn'], 'startedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'status' => ['shape' => 'JobPrimaryStatus'], 'stoppingCondition' => ['shape' => 'JobStoppingCondition'], 'tags' => ['shape' => 'TagsMap']]], 'GetJobResponseJobNameString' => ['type' => 'string', 'max' => 50, 'min' => 1, 'pattern' => '^[a-zA-Z0-9](-*[a-zA-Z0-9]){0,50}$'], 'GetQuantumTaskRequest' => ['type' => 'structure', 'required' => ['quantumTaskArn'], 'members' => ['quantumTaskArn' => ['shape' => 'QuantumTaskArn', 'location' => 'uri', 'locationName' => 'quantumTaskArn']]], 'GetQuantumTaskResponse' => ['type' => 'structure', 'required' => ['createdAt', 'deviceArn', 'deviceParameters', 'outputS3Bucket', 'outputS3Directory', 'quantumTaskArn', 'shots', 'status'], 'members' => ['createdAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'deviceArn' => ['shape' => 'DeviceArn'], 'deviceParameters' => ['shape' => 'JsonValue', 'jsonvalue' => \true], 'endedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'failureReason' => ['shape' => 'String'], 'jobArn' => ['shape' => 'JobArn'], 'outputS3Bucket' => ['shape' => 'String'], 'outputS3Directory' => ['shape' => 'String'], 'quantumTaskArn' => ['shape' => 'QuantumTaskArn'], 'shots' => ['shape' => 'Long'], 'status' => ['shape' => 'QuantumTaskStatus'], 'tags' => ['shape' => 'TagsMap']]], 'HyperParameters' => ['type' => 'map', 'key' => ['shape' => 'String256'], 'value' => ['shape' => 'HyperParametersValueString'], 'max' => 100, 'min' => 0], 'HyperParametersValueString' => ['type' => 'string', 'max' => 2500, 'min' => 1], 'InputConfigList' => ['type' => 'list', 'member' => ['shape' => 'InputFileConfig']], 'InputFileConfig' => ['type' => 'structure', 'required' => ['channelName', 'dataSource'], 'members' => ['channelName' => ['shape' => 'InputFileConfigChannelNameString'], 'contentType' => ['shape' => 'String256'], 'dataSource' => ['shape' => 'DataSource']]], 'InputFileConfigChannelNameString' => ['type' => 'string', 'max' => 64, 'min' => 1, 'pattern' => '^[A-Za-z0-9\\.\\-_]+$'], 'InstanceConfig' => ['type' => 'structure', 'required' => ['instanceType', 'volumeSizeInGb'], 'members' => ['instanceCount' => ['shape' => 'InstanceConfigInstanceCountInteger'], 'instanceType' => ['shape' => 'InstanceType'], 'volumeSizeInGb' => ['shape' => 'InstanceConfigVolumeSizeInGbInteger']]], 'InstanceConfigInstanceCountInteger' => ['type' => 'integer', 'box' => \true, 'min' => 1], 'InstanceConfigVolumeSizeInGbInteger' => ['type' => 'integer', 'box' => \true, 'min' => 1], 'InstanceType' => ['type' => 'string', 'enum' => ['ml.m4.xlarge', 'ml.m4.2xlarge', 'ml.m4.4xlarge', 'ml.m4.10xlarge', 'ml.m4.16xlarge', 'ml.g4dn.xlarge', 'ml.g4dn.2xlarge', 'ml.g4dn.4xlarge', 'ml.g4dn.8xlarge', 'ml.g4dn.12xlarge', 'ml.g4dn.16xlarge', 'ml.m5.large', 'ml.m5.xlarge', 'ml.m5.2xlarge', 'ml.m5.4xlarge', 'ml.m5.12xlarge', 'ml.m5.24xlarge', 'ml.c4.xlarge', 'ml.c4.2xlarge', 'ml.c4.4xlarge', 'ml.c4.8xlarge', 'ml.p2.xlarge', 'ml.p2.8xlarge', 'ml.p2.16xlarge', 'ml.p3.2xlarge', 'ml.p3.8xlarge', 'ml.p3.16xlarge', 'ml.p3dn.24xlarge', 'ml.p4d.24xlarge', 'ml.c5.xlarge', 'ml.c5.2xlarge', 'ml.c5.4xlarge', 'ml.c5.9xlarge', 'ml.c5.18xlarge', 'ml.c5n.xlarge', 'ml.c5n.2xlarge', 'ml.c5n.4xlarge', 'ml.c5n.9xlarge', 'ml.c5n.18xlarge']], 'Integer' => ['type' => 'integer', 'box' => \true], 'InternalServiceException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 500], 'exception' => \true, 'fault' => \true], 'JobArn' => ['type' => 'string', 'pattern' => '^arn:aws[a-z\\-]*:braket:[a-z0-9\\-]*:[0-9]{12}:job/.*$'], 'JobCheckpointConfig' => ['type' => 'structure', 'required' => ['s3Uri'], 'members' => ['localPath' => ['shape' => 'String4096'], 's3Uri' => ['shape' => 'S3Path']]], 'JobEventDetails' => ['type' => 'structure', 'members' => ['eventType' => ['shape' => 'JobEventType'], 'message' => ['shape' => 'JobEventDetailsMessageString'], 'timeOfEvent' => ['shape' => 'SyntheticTimestamp_date_time']]], 'JobEventDetailsMessageString' => ['type' => 'string', 'max' => 2500, 'min' => 0], 'JobEventType' => ['type' => 'string', 'enum' => ['WAITING_FOR_PRIORITY', 'QUEUED_FOR_EXECUTION', 'STARTING_INSTANCE', 'DOWNLOADING_DATA', 'RUNNING', 'DEPRIORITIZED_DUE_TO_INACTIVITY', 'UPLOADING_RESULTS', 'COMPLETED', 'FAILED', 'MAX_RUNTIME_EXCEEDED', 'CANCELLED']], 'JobEvents' => ['type' => 'list', 'member' => ['shape' => 'JobEventDetails'], 'max' => 20, 'min' => 0], 'JobOutputDataConfig' => ['type' => 'structure', 'required' => ['s3Path'], 'members' => ['kmsKeyId' => ['shape' => 'String2048'], 's3Path' => ['shape' => 'S3Path']]], 'JobPrimaryStatus' => ['type' => 'string', 'enum' => ['QUEUED', 'RUNNING', 'COMPLETED', 'FAILED', 'CANCELLING', 'CANCELLED']], 'JobStoppingCondition' => ['type' => 'structure', 'members' => ['maxRuntimeInSeconds' => ['shape' => 'JobStoppingConditionMaxRuntimeInSecondsInteger']]], 'JobStoppingConditionMaxRuntimeInSecondsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 432000, 'min' => 1], 'JobSummary' => ['type' => 'structure', 'required' => ['createdAt', 'device', 'jobArn', 'jobName', 'status'], 'members' => ['createdAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'device' => ['shape' => 'String256'], 'endedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'jobArn' => ['shape' => 'JobArn'], 'jobName' => ['shape' => 'String'], 'startedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'status' => ['shape' => 'JobPrimaryStatus'], 'tags' => ['shape' => 'TagsMap']]], 'JobSummaryList' => ['type' => 'list', 'member' => ['shape' => 'JobSummary']], 'JobToken' => ['type' => 'string', 'max' => 128, 'min' => 1], 'JsonValue' => ['type' => 'string'], 'ListTagsForResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn']]], 'ListTagsForResourceResponse' => ['type' => 'structure', 'members' => ['tags' => ['shape' => 'TagsMap']]], 'Long' => ['type' => 'long', 'box' => \true], 'QuantumTaskArn' => ['type' => 'string', 'max' => 256, 'min' => 1], 'QuantumTaskStatus' => ['type' => 'string', 'enum' => ['CREATED', 'QUEUED', 'RUNNING', 'COMPLETED', 'FAILED', 'CANCELLING', 'CANCELLED']], 'QuantumTaskSummary' => ['type' => 'structure', 'required' => ['createdAt', 'deviceArn', 'outputS3Bucket', 'outputS3Directory', 'quantumTaskArn', 'shots', 'status'], 'members' => ['createdAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'deviceArn' => ['shape' => 'DeviceArn'], 'endedAt' => ['shape' => 'SyntheticTimestamp_date_time'], 'outputS3Bucket' => ['shape' => 'String'], 'outputS3Directory' => ['shape' => 'String'], 'quantumTaskArn' => ['shape' => 'QuantumTaskArn'], 'shots' => ['shape' => 'Long'], 'status' => ['shape' => 'QuantumTaskStatus'], 'tags' => ['shape' => 'TagsMap']]], 'QuantumTaskSummaryList' => ['type' => 'list', 'member' => ['shape' => 'QuantumTaskSummary']], 'ResourceNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 404, 'senderFault' => \true], 'exception' => \true], 'RoleArn' => ['type' => 'string', 'pattern' => '^arn:aws[a-z\\-]*:iam::\\d{12}:role/?[a-zA-Z_0-9+=,.@\\-_/]+$'], 'S3DataSource' => ['type' => 'structure', 'required' => ['s3Uri'], 'members' => ['s3Uri' => ['shape' => 'S3Path']]], 'S3Path' => ['type' => 'string', 'max' => 1024, 'min' => 0, 'pattern' => '^(https|s3)://([^/]+)/?(.*)$'], 'ScriptModeConfig' => ['type' => 'structure', 'required' => ['entryPoint', 's3Uri'], 'members' => ['compressionType' => ['shape' => 'CompressionType'], 'entryPoint' => ['shape' => 'String'], 's3Uri' => ['shape' => 'S3Path']]], 'SearchDevicesFilter' => ['type' => 'structure', 'required' => ['name', 'values'], 'members' => ['name' => ['shape' => 'SearchDevicesFilterNameString'], 'values' => ['shape' => 'SearchDevicesFilterValuesList']]], 'SearchDevicesFilterNameString' => ['type' => 'string', 'max' => 64, 'min' => 1], 'SearchDevicesFilterValuesList' => ['type' => 'list', 'member' => ['shape' => 'String256'], 'max' => 10, 'min' => 1], 'SearchDevicesRequest' => ['type' => 'structure', 'required' => ['filters'], 'members' => ['filters' => ['shape' => 'SearchDevicesRequestFiltersList'], 'maxResults' => ['shape' => 'SearchDevicesRequestMaxResultsInteger'], 'nextToken' => ['shape' => 'String']]], 'SearchDevicesRequestFiltersList' => ['type' => 'list', 'member' => ['shape' => 'SearchDevicesFilter'], 'max' => 10, 'min' => 0], 'SearchDevicesRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'SearchDevicesResponse' => ['type' => 'structure', 'required' => ['devices'], 'members' => ['devices' => ['shape' => 'DeviceSummaryList'], 'nextToken' => ['shape' => 'String']]], 'SearchJobsFilter' => ['type' => 'structure', 'required' => ['name', 'operator', 'values'], 'members' => ['name' => ['shape' => 'String64'], 'operator' => ['shape' => 'SearchJobsFilterOperator'], 'values' => ['shape' => 'SearchJobsFilterValuesList']]], 'SearchJobsFilterOperator' => ['type' => 'string', 'enum' => ['LT', 'LTE', 'EQUAL', 'GT', 'GTE', 'BETWEEN', 'CONTAINS']], 'SearchJobsFilterValuesList' => ['type' => 'list', 'member' => ['shape' => 'String256'], 'max' => 10, 'min' => 1], 'SearchJobsRequest' => ['type' => 'structure', 'required' => ['filters'], 'members' => ['filters' => ['shape' => 'SearchJobsRequestFiltersList'], 'maxResults' => ['shape' => 'SearchJobsRequestMaxResultsInteger'], 'nextToken' => ['shape' => 'String']]], 'SearchJobsRequestFiltersList' => ['type' => 'list', 'member' => ['shape' => 'SearchJobsFilter'], 'max' => 10, 'min' => 0], 'SearchJobsRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'SearchJobsResponse' => ['type' => 'structure', 'required' => ['jobs'], 'members' => ['jobs' => ['shape' => 'JobSummaryList'], 'nextToken' => ['shape' => 'String']]], 'SearchQuantumTasksFilter' => ['type' => 'structure', 'required' => ['name', 'operator', 'values'], 'members' => ['name' => ['shape' => 'String64'], 'operator' => ['shape' => 'SearchQuantumTasksFilterOperator'], 'values' => ['shape' => 'SearchQuantumTasksFilterValuesList']]], 'SearchQuantumTasksFilterOperator' => ['type' => 'string', 'enum' => ['LT', 'LTE', 'EQUAL', 'GT', 'GTE', 'BETWEEN']], 'SearchQuantumTasksFilterValuesList' => ['type' => 'list', 'member' => ['shape' => 'String256'], 'max' => 10, 'min' => 1], 'SearchQuantumTasksRequest' => ['type' => 'structure', 'required' => ['filters'], 'members' => ['filters' => ['shape' => 'SearchQuantumTasksRequestFiltersList'], 'maxResults' => ['shape' => 'SearchQuantumTasksRequestMaxResultsInteger'], 'nextToken' => ['shape' => 'String']]], 'SearchQuantumTasksRequestFiltersList' => ['type' => 'list', 'member' => ['shape' => 'SearchQuantumTasksFilter'], 'max' => 10, 'min' => 0], 'SearchQuantumTasksRequestMaxResultsInteger' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'SearchQuantumTasksResponse' => ['type' => 'structure', 'required' => ['quantumTasks'], 'members' => ['nextToken' => ['shape' => 'String'], 'quantumTasks' => ['shape' => 'QuantumTaskSummaryList']]], 'ServiceQuotaExceededException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 402, 'senderFault' => \true], 'exception' => \true], 'String' => ['type' => 'string'], 'String1024' => ['type' => 'string', 'max' => 1024, 'min' => 1], 'String2048' => ['type' => 'string', 'max' => 2048, 'min' => 1], 'String256' => ['type' => 'string', 'max' => 256, 'min' => 1], 'String4096' => ['type' => 'string', 'max' => 4096, 'min' => 1], 'String64' => ['type' => 'string', 'max' => 64, 'min' => 1], 'SyntheticTimestamp_date_time' => ['type' => 'timestamp', 'timestampFormat' => 'iso8601'], 'TagKeys' => ['type' => 'list', 'member' => ['shape' => 'String']], 'TagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tags'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tags' => ['shape' => 'TagsMap']]], 'TagResourceResponse' => ['type' => 'structure', 'members' => []], 'TagsMap' => ['type' => 'map', 'key' => ['shape' => 'String'], 'value' => ['shape' => 'String']], 'ThrottlingException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 429, 'senderFault' => \true], 'exception' => \true], 'UntagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tagKeys'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tagKeys' => ['shape' => 'TagKeys', 'location' => 'querystring', 'locationName' => 'tagKeys']]], 'UntagResourceResponse' => ['type' => 'structure', 'members' => []], 'Uri' => ['type' => 'string', 'max' => 255, 'min' => 1, 'pattern' => '\\d{10,14}\\.dkr\\.ecr.[a-z0-9-]+\\.amazonaws\\.com\\/.+(@sha256)?:.+'], 'ValidationException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 400, 'senderFault' => \true], 'exception' => \true]]];