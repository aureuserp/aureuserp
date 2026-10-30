<?php

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

dataset('crm resources', [
    'configuration sales teams'   => [TeamResource::class, 'team'],
    'configuration stages'        => [StageResource::class, 'stage'],
    'configuration tags'          => [TagResource::class, 'tag'],
    'configuration lost reasons'  => [LostReasonResource::class, 'lost::reason'],
    'configuration activity type' => [ActivityTypeResource::class, 'activity::type'],
    'configuration activity plan' => [ActivityPlanResource::class, 'activity::plan'],
    'sales pipeline'              => [OpportunityResource::class, 'opportunity'],
    'sales activities'            => [ActivityResource::class, 'activity'],
    'sales quotations'            => [QuotationResource::class, 'quotation'],
    'sales teams'                 => [SalesTeamResource::class, 'team'],
    'sales customers'             => [CustomerResource::class, 'customer'],
    'leads'                       => [LeadResource::class, 'lead'],
    'reporting forecast'          => [ForecastResource::class, 'forecast'],
    'reporting pipeline'          => [PipelineAnalysisResource::class, 'pipeline::analysis'],
    'reporting leads'             => [LeadAnalysisResource::class, 'lead::analysis'],
    'reporting activities'        => [ActivityAnalysisResource::class, 'activity::analysis'],
]);

it('denies a crm resource to a user without permissions', function (string $resource, string $suffix) {
    CRMHelper::actingAs();

    expect($resource::canViewAny())->toBeFalse();
})->with('crm resources');

it('grants a crm resource with its view_any crm permission', function (string $resource, string $suffix) {
    CRMHelper::actingAs(["view_any_crm_{$suffix}"]);

    expect($resource::canViewAny())->toBeTrue();
})->with('crm resources');

it('does not grant a crm resource with another crm resource permission', function (string $resource, string $suffix) {
    $otherSuffix = $suffix === 'stage' ? 'tag' : 'stage';

    CRMHelper::actingAs(CRMHelper::permissionsFor([$otherSuffix]));

    expect($resource::canViewAny())->toBeFalse();
})->with('crm resources');

it('does not grant a reused resource with the source plugin permissions alone', function (string $resource, array $sourcePermissions) {
    expect(in_array(HasCRMAuthorization::class, class_uses_recursive($resource), true))->toBeTrue();

    CRMHelper::actingAs($sourcePermissions);

    expect($resource::canViewAny())->toBeFalse()
        ->and($resource::canCreate())->toBeFalse();
})->with([
    'contacts' => [CustomerResource::class, [
        'view_any_contact_partner', 'view_contact_partner', 'create_contact_partner', 'update_contact_partner',
    ]],
    'quotations' => [QuotationResource::class, [
        'view_any_sale_order', 'view_sale_order', 'create_sale_order',
        'view_any_sale_quotation', 'view_sale_quotation', 'create_sale_quotation',
    ]],
    'configuration sales teams' => [TeamResource::class, [
        'view_any_sale_team', 'view_sale_team', 'create_sale_team', 'update_sale_team',
    ]],
    'sales teams overview' => [SalesTeamResource::class, [
        'view_any_sale_team', 'view_sale_team', 'create_sale_team', 'update_sale_team',
    ]],
]);

it('does not grant the crm activity types and plans with the sales or support permissions', function (string $resource, array $sourcePermissions) {
    CRMHelper::actingAs($sourcePermissions);

    expect($resource::canViewAny())->toBeFalse()
        ->and($resource::canCreate())->toBeFalse();
})->with([
    'activity types' => [ActivityTypeResource::class, [
        'view_any_sale_activity::type', 'create_sale_activity::type',
        'view_any_support_activity::type', 'create_support_activity::type',
    ]],
    'activity plans' => [ActivityPlanResource::class, [
        'view_any_sale_activity::plan', 'create_sale_activity::plan',
        'view_any_support_activity::plan', 'create_support_activity::plan',
    ]],
]);

it('grants create on a reused resource with the crm create permission', function (string $resource, string $suffix) {
    CRMHelper::actingAs(["view_any_crm_{$suffix}", "create_crm_{$suffix}"]);

    expect($resource::canCreate())->toBeTrue();
})->with([
    'customers'  => [CustomerResource::class, 'customer'],
    'quotations' => [QuotationResource::class, 'quotation'],
    'teams'      => [TeamResource::class, 'team'],
]);

it('denies abilities the crm policy does not define', function () {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['forecast']));

    expect(ForecastResource::canViewAny())->toBeTrue()
        ->and(ForecastResource::canCreate())->toBeFalse()
        ->and(ForecastResource::canDeleteAny())->toBeFalse();
});
