<?php

namespace WPStack_Connect_Vendor\Aws\AuditManager;

use WPStack_Connect_Vendor\Aws\AwsClient;
/**
 * This client is used to interact with the **AWS Audit Manager** service.
 * @method \Aws\Result associateAssessmentReportEvidenceFolder(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateAssessmentReportEvidenceFolderAsync(array $args = [])
 * @method \Aws\Result batchAssociateAssessmentReportEvidence(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchAssociateAssessmentReportEvidenceAsync(array $args = [])
 * @method \Aws\Result batchCreateDelegationByAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchCreateDelegationByAssessmentAsync(array $args = [])
 * @method \Aws\Result batchDeleteDelegationByAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchDeleteDelegationByAssessmentAsync(array $args = [])
 * @method \Aws\Result batchDisassociateAssessmentReportEvidence(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchDisassociateAssessmentReportEvidenceAsync(array $args = [])
 * @method \Aws\Result batchImportEvidenceToAssessmentControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchImportEvidenceToAssessmentControlAsync(array $args = [])
 * @method \Aws\Result createAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAssessmentAsync(array $args = [])
 * @method \Aws\Result createAssessmentFramework(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAssessmentFrameworkAsync(array $args = [])
 * @method \Aws\Result createAssessmentReport(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAssessmentReportAsync(array $args = [])
 * @method \Aws\Result createControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createControlAsync(array $args = [])
 * @method \Aws\Result deleteAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAssessmentAsync(array $args = [])
 * @method \Aws\Result deleteAssessmentFramework(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAssessmentFrameworkAsync(array $args = [])
 * @method \Aws\Result deleteAssessmentFrameworkShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAssessmentFrameworkShareAsync(array $args = [])
 * @method \Aws\Result deleteAssessmentReport(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAssessmentReportAsync(array $args = [])
 * @method \Aws\Result deleteControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteControlAsync(array $args = [])
 * @method \Aws\Result deregisterAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deregisterAccountAsync(array $args = [])
 * @method \Aws\Result deregisterOrganizationAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deregisterOrganizationAdminAccountAsync(array $args = [])
 * @method \Aws\Result disassociateAssessmentReportEvidenceFolder(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateAssessmentReportEvidenceFolderAsync(array $args = [])
 * @method \Aws\Result getAccountStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccountStatusAsync(array $args = [])
 * @method \Aws\Result getAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAssessmentAsync(array $args = [])
 * @method \Aws\Result getAssessmentFramework(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAssessmentFrameworkAsync(array $args = [])
 * @method \Aws\Result getAssessmentReportUrl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAssessmentReportUrlAsync(array $args = [])
 * @method \Aws\Result getChangeLogs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getChangeLogsAsync(array $args = [])
 * @method \Aws\Result getControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getControlAsync(array $args = [])
 * @method \Aws\Result getDelegations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDelegationsAsync(array $args = [])
 * @method \Aws\Result getEvidence(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceAsync(array $args = [])
 * @method \Aws\Result getEvidenceByEvidenceFolder(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceByEvidenceFolderAsync(array $args = [])
 * @method \Aws\Result getEvidenceFileUploadUrl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceFileUploadUrlAsync(array $args = [])
 * @method \Aws\Result getEvidenceFolder(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceFolderAsync(array $args = [])
 * @method \Aws\Result getEvidenceFoldersByAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceFoldersByAssessmentAsync(array $args = [])
 * @method \Aws\Result getEvidenceFoldersByAssessmentControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEvidenceFoldersByAssessmentControlAsync(array $args = [])
 * @method \Aws\Result getInsights(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getInsightsAsync(array $args = [])
 * @method \Aws\Result getInsightsByAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getInsightsByAssessmentAsync(array $args = [])
 * @method \Aws\Result getOrganizationAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getOrganizationAdminAccountAsync(array $args = [])
 * @method \Aws\Result getServicesInScope(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getServicesInScopeAsync(array $args = [])
 * @method \Aws\Result getSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSettingsAsync(array $args = [])
 * @method \Aws\Result listAssessmentControlInsightsByControlDomain(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAssessmentControlInsightsByControlDomainAsync(array $args = [])
 * @method \Aws\Result listAssessmentFrameworkShareRequests(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAssessmentFrameworkShareRequestsAsync(array $args = [])
 * @method \Aws\Result listAssessmentFrameworks(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAssessmentFrameworksAsync(array $args = [])
 * @method \Aws\Result listAssessmentReports(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAssessmentReportsAsync(array $args = [])
 * @method \Aws\Result listAssessments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAssessmentsAsync(array $args = [])
 * @method \Aws\Result listControlDomainInsights(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listControlDomainInsightsAsync(array $args = [])
 * @method \Aws\Result listControlDomainInsightsByAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listControlDomainInsightsByAssessmentAsync(array $args = [])
 * @method \Aws\Result listControlInsightsByControlDomain(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listControlInsightsByControlDomainAsync(array $args = [])
 * @method \Aws\Result listControls(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listControlsAsync(array $args = [])
 * @method \Aws\Result listKeywordsForDataSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listKeywordsForDataSourceAsync(array $args = [])
 * @method \Aws\Result listNotifications(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listNotificationsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result registerAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise registerAccountAsync(array $args = [])
 * @method \Aws\Result registerOrganizationAdminAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise registerOrganizationAdminAccountAsync(array $args = [])
 * @method \Aws\Result startAssessmentFrameworkShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startAssessmentFrameworkShareAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateAssessment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentAsync(array $args = [])
 * @method \Aws\Result updateAssessmentControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentControlAsync(array $args = [])
 * @method \Aws\Result updateAssessmentControlSetStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentControlSetStatusAsync(array $args = [])
 * @method \Aws\Result updateAssessmentFramework(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentFrameworkAsync(array $args = [])
 * @method \Aws\Result updateAssessmentFrameworkShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentFrameworkShareAsync(array $args = [])
 * @method \Aws\Result updateAssessmentStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateAssessmentStatusAsync(array $args = [])
 * @method \Aws\Result updateControl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateControlAsync(array $args = [])
 * @method \Aws\Result updateSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateSettingsAsync(array $args = [])
 * @method \Aws\Result validateAssessmentReportIntegrity(array $args = [])
 * @method \GuzzleHttp\Promise\Promise validateAssessmentReportIntegrityAsync(array $args = [])
 */
class AuditManagerClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
}
