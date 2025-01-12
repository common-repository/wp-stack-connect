<?php

namespace WPStack_Connect_Vendor\Aws\ServiceCatalog;

use WPStack_Connect_Vendor\Aws\AwsClient;
/**
 * This client is used to interact with the **AWS Service Catalog** service.
 * @method \Aws\Result acceptPortfolioShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise acceptPortfolioShareAsync(array $args = [])
 * @method \Aws\Result associateBudgetWithResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateBudgetWithResourceAsync(array $args = [])
 * @method \Aws\Result associatePrincipalWithPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associatePrincipalWithPortfolioAsync(array $args = [])
 * @method \Aws\Result associateProductWithPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateProductWithPortfolioAsync(array $args = [])
 * @method \Aws\Result associateServiceActionWithProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateServiceActionWithProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result associateTagOptionWithResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateTagOptionWithResourceAsync(array $args = [])
 * @method \Aws\Result batchAssociateServiceActionWithProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchAssociateServiceActionWithProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result batchDisassociateServiceActionFromProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchDisassociateServiceActionFromProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result copyProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise copyProductAsync(array $args = [])
 * @method \Aws\Result createConstraint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createConstraintAsync(array $args = [])
 * @method \Aws\Result createPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createPortfolioAsync(array $args = [])
 * @method \Aws\Result createPortfolioShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createPortfolioShareAsync(array $args = [])
 * @method \Aws\Result createProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProductAsync(array $args = [])
 * @method \Aws\Result createProvisionedProductPlan(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProvisionedProductPlanAsync(array $args = [])
 * @method \Aws\Result createProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result createServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createServiceActionAsync(array $args = [])
 * @method \Aws\Result createTagOption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createTagOptionAsync(array $args = [])
 * @method \Aws\Result deleteConstraint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteConstraintAsync(array $args = [])
 * @method \Aws\Result deletePortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePortfolioAsync(array $args = [])
 * @method \Aws\Result deletePortfolioShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePortfolioShareAsync(array $args = [])
 * @method \Aws\Result deleteProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProductAsync(array $args = [])
 * @method \Aws\Result deleteProvisionedProductPlan(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProvisionedProductPlanAsync(array $args = [])
 * @method \Aws\Result deleteProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result deleteServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteServiceActionAsync(array $args = [])
 * @method \Aws\Result deleteTagOption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteTagOptionAsync(array $args = [])
 * @method \Aws\Result describeConstraint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeConstraintAsync(array $args = [])
 * @method \Aws\Result describeCopyProductStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeCopyProductStatusAsync(array $args = [])
 * @method \Aws\Result describePortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePortfolioAsync(array $args = [])
 * @method \Aws\Result describePortfolioShareStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePortfolioShareStatusAsync(array $args = [])
 * @method \Aws\Result describePortfolioShares(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePortfolioSharesAsync(array $args = [])
 * @method \Aws\Result describeProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProductAsync(array $args = [])
 * @method \Aws\Result describeProductAsAdmin(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProductAsAdminAsync(array $args = [])
 * @method \Aws\Result describeProductView(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProductViewAsync(array $args = [])
 * @method \Aws\Result describeProvisionedProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProvisionedProductAsync(array $args = [])
 * @method \Aws\Result describeProvisionedProductPlan(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProvisionedProductPlanAsync(array $args = [])
 * @method \Aws\Result describeProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result describeProvisioningParameters(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProvisioningParametersAsync(array $args = [])
 * @method \Aws\Result describeRecord(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeRecordAsync(array $args = [])
 * @method \Aws\Result describeServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeServiceActionAsync(array $args = [])
 * @method \Aws\Result describeServiceActionExecutionParameters(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeServiceActionExecutionParametersAsync(array $args = [])
 * @method \Aws\Result describeTagOption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeTagOptionAsync(array $args = [])
 * @method \Aws\Result disableAWSOrganizationsAccess(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disableAWSOrganizationsAccessAsync(array $args = [])
 * @method \Aws\Result disassociateBudgetFromResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateBudgetFromResourceAsync(array $args = [])
 * @method \Aws\Result disassociatePrincipalFromPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociatePrincipalFromPortfolioAsync(array $args = [])
 * @method \Aws\Result disassociateProductFromPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateProductFromPortfolioAsync(array $args = [])
 * @method \Aws\Result disassociateServiceActionFromProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateServiceActionFromProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result disassociateTagOptionFromResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateTagOptionFromResourceAsync(array $args = [])
 * @method \Aws\Result enableAWSOrganizationsAccess(array $args = [])
 * @method \GuzzleHttp\Promise\Promise enableAWSOrganizationsAccessAsync(array $args = [])
 * @method \Aws\Result executeProvisionedProductPlan(array $args = [])
 * @method \GuzzleHttp\Promise\Promise executeProvisionedProductPlanAsync(array $args = [])
 * @method \Aws\Result executeProvisionedProductServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise executeProvisionedProductServiceActionAsync(array $args = [])
 * @method \Aws\Result getAWSOrganizationsAccessStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAWSOrganizationsAccessStatusAsync(array $args = [])
 * @method \Aws\Result getProvisionedProductOutputs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getProvisionedProductOutputsAsync(array $args = [])
 * @method \Aws\Result importAsProvisionedProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise importAsProvisionedProductAsync(array $args = [])
 * @method \Aws\Result listAcceptedPortfolioShares(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAcceptedPortfolioSharesAsync(array $args = [])
 * @method \Aws\Result listBudgetsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBudgetsForResourceAsync(array $args = [])
 * @method \Aws\Result listConstraintsForPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listConstraintsForPortfolioAsync(array $args = [])
 * @method \Aws\Result listLaunchPaths(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listLaunchPathsAsync(array $args = [])
 * @method \Aws\Result listOrganizationPortfolioAccess(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listOrganizationPortfolioAccessAsync(array $args = [])
 * @method \Aws\Result listPortfolioAccess(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPortfolioAccessAsync(array $args = [])
 * @method \Aws\Result listPortfolios(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPortfoliosAsync(array $args = [])
 * @method \Aws\Result listPortfoliosForProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPortfoliosForProductAsync(array $args = [])
 * @method \Aws\Result listPrincipalsForPortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPrincipalsForPortfolioAsync(array $args = [])
 * @method \Aws\Result listProvisionedProductPlans(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProvisionedProductPlansAsync(array $args = [])
 * @method \Aws\Result listProvisioningArtifacts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProvisioningArtifactsAsync(array $args = [])
 * @method \Aws\Result listProvisioningArtifactsForServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProvisioningArtifactsForServiceActionAsync(array $args = [])
 * @method \Aws\Result listRecordHistory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRecordHistoryAsync(array $args = [])
 * @method \Aws\Result listResourcesForTagOption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourcesForTagOptionAsync(array $args = [])
 * @method \Aws\Result listServiceActions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceActionsAsync(array $args = [])
 * @method \Aws\Result listServiceActionsForProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceActionsForProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result listStackInstancesForProvisionedProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listStackInstancesForProvisionedProductAsync(array $args = [])
 * @method \Aws\Result listTagOptions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagOptionsAsync(array $args = [])
 * @method \Aws\Result notifyProvisionProductEngineWorkflowResult(array $args = [])
 * @method \GuzzleHttp\Promise\Promise notifyProvisionProductEngineWorkflowResultAsync(array $args = [])
 * @method \Aws\Result notifyTerminateProvisionedProductEngineWorkflowResult(array $args = [])
 * @method \GuzzleHttp\Promise\Promise notifyTerminateProvisionedProductEngineWorkflowResultAsync(array $args = [])
 * @method \Aws\Result notifyUpdateProvisionedProductEngineWorkflowResult(array $args = [])
 * @method \GuzzleHttp\Promise\Promise notifyUpdateProvisionedProductEngineWorkflowResultAsync(array $args = [])
 * @method \Aws\Result provisionProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise provisionProductAsync(array $args = [])
 * @method \Aws\Result rejectPortfolioShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise rejectPortfolioShareAsync(array $args = [])
 * @method \Aws\Result scanProvisionedProducts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise scanProvisionedProductsAsync(array $args = [])
 * @method \Aws\Result searchProducts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchProductsAsync(array $args = [])
 * @method \Aws\Result searchProductsAsAdmin(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchProductsAsAdminAsync(array $args = [])
 * @method \Aws\Result searchProvisionedProducts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchProvisionedProductsAsync(array $args = [])
 * @method \Aws\Result terminateProvisionedProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise terminateProvisionedProductAsync(array $args = [])
 * @method \Aws\Result updateConstraint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateConstraintAsync(array $args = [])
 * @method \Aws\Result updatePortfolio(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updatePortfolioAsync(array $args = [])
 * @method \Aws\Result updatePortfolioShare(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updatePortfolioShareAsync(array $args = [])
 * @method \Aws\Result updateProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProductAsync(array $args = [])
 * @method \Aws\Result updateProvisionedProduct(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProvisionedProductAsync(array $args = [])
 * @method \Aws\Result updateProvisionedProductProperties(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProvisionedProductPropertiesAsync(array $args = [])
 * @method \Aws\Result updateProvisioningArtifact(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProvisioningArtifactAsync(array $args = [])
 * @method \Aws\Result updateServiceAction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateServiceActionAsync(array $args = [])
 * @method \Aws\Result updateTagOption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateTagOptionAsync(array $args = [])
 */
class ServiceCatalogClient extends \WPStack_Connect_Vendor\Aws\AwsClient
{
}
