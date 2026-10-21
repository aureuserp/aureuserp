<?php

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

dataset('crm resources', [
    'configuration sales teams'   => [TeamResource::class, 'team'],
    'configuration stages'        => [StageResource::class, 'stage'],
    'configuration tags'          => [TagResource::class, 'tag'],
    'configuration lost reasons'  => [LostReasonResource::class, 'lost::reason'],
    'configuration activity type' => [ActivityTypeResource::class, 'activity::type'],
    'configuration activity plan' => [ActivityPlanResource::class, 'activity::plan'],
    'configuration users'         => [UserResource::class, 'user'],
    'configuration companies'     => [CompanyResource::class, 'company'],
    'sales pipeline'              => [OpportunityResource::class, 'opportunity'],
    'sales activities'            => [ActivityResource::class, 'activity'],
    'sales quotations'            => [QuotationResource::class, 'quotation'],
    'sales teams'                 => [SalesTeamResource::class, 'team'],
    'sales contacts'              => [ContactResource::class, 'contact'],
    'leads'                       => [LeadResource::class, 'lead'],
    'reporting forecast'          => [ForecastResource::class, 'forecast'],
    'reporting pipeline'          => [PipelineAnalysisResource::class, 'pipeline::analysis'],
    'reporting leads'             => [LeadAnalysisResource::class, 'lead::analysis'],
    'reporting activities'        => [ActivityAnalysisResource::class, 'activity::analysis'],
]);

it('denies a crm resource to a user without permissions', function (string $resource, string $suffix) {
    CrmTestHelper::actingAs();

    expect($resource::canViewAny())->toBeFalse();
})->with('crm resources');

it('grants a crm resource with its view_any crm permission', function (string $resource, string $suffix) {
    CrmTestHelper::actingAs(["view_any_crm_{$suffix}"]);

    expect($resource::canViewAny())->toBeTrue();
})->with('crm resources');

it('does not grant a crm resource with another crm resource permission', function (string $resource, string $suffix) {
    $otherSuffix = $suffix === 'stage' ? 'tag' : 'stage';

    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor([$otherSuffix]));

    expect($resource::canViewAny())->toBeFalse();
})->with('crm resources');

it('does not grant a reused resource with the source plugin permissions alone', function (string $resource, array $sourcePermissions) {
    expect(in_array(HasCrmAuthorization::class, class_uses_recursive($resource), true))->toBeTrue();

    CrmTestHelper::actingAs($sourcePermissions);

    expect($resource::canViewAny())->toBeFalse()
        ->and($resource::canCreate())->toBeFalse();
})->with([
    'contacts' => [ContactResource::class, [
        'view_any_contact_partner', 'view_contact_partner', 'create_contact_partner', 'update_contact_partner',
    ]],
    'quotations' => [QuotationResource::class, [
        'view_any_sale_order', 'view_sale_order', 'create_sale_order',
        'view_any_sale_quotation', 'view_sale_quotation', 'create_sale_quotation',
    ]],
    'users' => [UserResource::class, [
        'view_any_security_user', 'view_security_user', 'create_security_user', 'update_security_user',
    ]],
    'companies' => [CompanyResource::class, [
        'view_any_security_company', 'view_security_company', 'create_security_company', 'update_security_company',
    ]],
    'configuration sales teams' => [TeamResource::class, [
        'view_any_sale_team', 'view_sale_team', 'create_sale_team', 'update_sale_team',
    ]],
    'sales teams overview' => [SalesTeamResource::class, [
        'view_any_sale_team', 'view_sale_team', 'create_sale_team', 'update_sale_team',
    ]],
]);

it('does not grant the crm activity types and plans with the sales or support permissions', function (string $resource, array $sourcePermissions) {
    CrmTestHelper::actingAs($sourcePermissions);

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
    CrmTestHelper::actingAs(["view_any_crm_{$suffix}", "create_crm_{$suffix}"]);

    expect($resource::canCreate())->toBeTrue();
})->with([
    'contacts'   => [ContactResource::class, 'contact'],
    'quotations' => [QuotationResource::class, 'quotation'],
    'users'      => [UserResource::class, 'user'],
    'companies'  => [CompanyResource::class, 'company'],
    'teams'      => [TeamResource::class, 'team'],
]);

it('denies abilities the crm policy does not define', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['forecast']));

    expect(ForecastResource::canViewAny())->toBeTrue()
        ->and(ForecastResource::canCreate())->toBeFalse()
        ->and(ForecastResource::canDeleteAny())->toBeFalse();
});
