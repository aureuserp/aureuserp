<?php

use Filament\Facades\Filament;
use Filament\Pages\Enums\SubNavigationPosition;
use Illuminate\Support\Facades\Lang;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\CreateLead;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;
use Webkul\Support\Enums\NavigationGroup;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

dataset('crm clusters', [
    'sales'         => [Sales::class, 1, 'crm::filament/clusters/sales.navigation.title', 'المبيعات'],
    'leads'         => [Leads::class, 2, 'crm::filament/clusters/leads.navigation.title', 'العملاء المحتملون'],
    'reporting'     => [Reporting::class, 3, 'crm::filament/clusters/reporting.navigation.title', 'التقارير'],
    'configuration' => [Configurations::class, 4, 'crm::filament/clusters/configurations.navigation.title', 'الإعدادات'],
]);

it('places each cluster in the crm navigation group in order', function (string $cluster, int $sort) {
    expect($cluster::getNavigationSort())->toBe($sort)
        ->and($cluster::getNavigationGroup())->toBe(NavigationGroup::Crm);
})->with('crm clusters');

it('registers every crm cluster in the admin panel', function (string $cluster) {
    CrmTestHelper::actingAs();

    expect(Filament::getPanel('admin')->getClusters())->toContain($cluster);
})->with('crm clusters');

it('translates the cluster label and breadcrumb', function (string $cluster, int $sort, string $key, string $arabic) {
    expect(Lang::has($key))->toBeTrue()
        ->and($cluster::getNavigationLabel())->toBe(__($key))
        ->and($cluster::getNavigationLabel())->not->toBe($key)
        ->and($cluster::getClusterBreadcrumb())->toBe($cluster::getNavigationLabel());

    app()->setLocale('ar');

    expect($cluster::getNavigationLabel())->toBe($arabic)
        ->and($cluster::getClusterBreadcrumb())->toBe($arabic);
})->with('crm clusters');

it('shows no left menu in the leads section, which has a single item', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    expect(Leads::getClusteredComponents())->toContain(LeadResource::class);

    $page = app(CreateLead::class);

    expect($page->getSubNavigation())->toBe([])
        ->and(CreateLead::getSubNavigationPosition())->toBe(SubNavigationPosition::Start);
});

it('shows the left menu in the configuration section when it has more than one item', function () {
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['team']),
        'view_any_crm_stage',
        'view_any_crm_tag',
    ]);

    $navigation = app(CreateTeam::class)->getSubNavigation();

    expect(count($navigation))->toBeGreaterThan(1)
        ->and(CreateTeam::getSubNavigationPosition())->toBe(SubNavigationPosition::Start);
});

it('hides the left menu when the user can reach only one item of the section', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['team']));

    expect(app(CreateTeam::class)->getSubNavigation())->toBe([]);
});

it('takes resource labels from the crm translations with a real plural', function (string $resource, string $key, string $arabicPlural) {
    $declaringClass = (new ReflectionMethod($resource, 'getPluralModelLabel'))->getDeclaringClass()->getName();

    expect($declaringClass)->toBe($resource)
        ->and(Lang::has("{$key}.model-label"))->toBeTrue()
        ->and(Lang::has("{$key}.plural-model-label"))->toBeTrue()
        ->and($resource::getModelLabel())->toBe(__("{$key}.model-label"))
        ->and($resource::getPluralModelLabel())->toBe(__("{$key}.plural-model-label"))
        ->and($resource::getPluralModelLabel())->not->toBe($resource::getModelLabel());

    app()->setLocale('ar');

    expect($resource::getPluralModelLabel())->toBe($arabicPlural)
        ->and($resource::getPluralModelLabel())->not->toBe($resource::getModelLabel())
        ->and($resource::getPluralModelLabel())->not->toBe($resource::getModelLabel().'s');
})->with([
    'sales teams'     => [TeamResource::class, 'crm::filament/clusters/configurations/resources/team', 'فرق المبيعات'],
    'stages'          => [StageResource::class, 'crm::filament/clusters/configurations/resources/stage', 'المراحل'],
    'tags'            => [TagResource::class, 'crm::filament/clusters/configurations/resources/tag', 'الوسوم'],
    'lost reasons'    => [LostReasonResource::class, 'crm::filament/clusters/configurations/resources/lost-reason', 'أسباب الخسارة'],
    'activity types'  => [ActivityTypeResource::class, 'crm::filament/clusters/configurations/resources/activity-type', 'أنواع الأنشطة'],
    'activity plans'  => [ActivityPlanResource::class, 'crm::filament/clusters/configurations/resources/activity-plan', 'خطط الأنشطة'],
    'users'           => [UserResource::class, 'crm::filament/clusters/configurations/resources/user', 'المستخدمون'],
    'companies'       => [CompanyResource::class, 'crm::filament/clusters/configurations/resources/company', 'الشركات'],
    'leads'           => [LeadResource::class, 'crm::filament/clusters/leads/resources/lead', 'العملاء المحتملون'],
    'pipeline'        => [OpportunityResource::class, 'crm::filament/clusters/sales/resources/opportunity', 'الفرص'],
    'activities'      => [ActivityResource::class, 'crm::filament/clusters/sales/resources/activity', 'الأنشطة'],
    'contacts'        => [ContactResource::class, 'crm::filament/clusters/sales/resources/contact', 'جهات الاتصال'],
    'sales overview'  => [SalesTeamResource::class, 'crm::filament/clusters/sales/resources/team', 'فرق المبيعات'],
]);

it('uses the report title as both labels of a report resource', function (string $resource, string $key) {
    expect(Lang::has("{$key}.title"))->toBeTrue()
        ->and($resource::getModelLabel())->toBe(__("{$key}.title"))
        ->and($resource::getPluralModelLabel())->toBe(__("{$key}.title"))
        ->and($resource::getPluralModelLabel())->not->toBe($resource::getModelLabel().'s');
})->with([
    'forecast'          => [ForecastResource::class, 'crm::filament/clusters/reporting/resources/forecast'],
    'pipeline analysis' => [PipelineAnalysisResource::class, 'crm::filament/clusters/reporting/resources/pipeline-analysis'],
    'lead analysis'     => [LeadAnalysisResource::class, 'crm::filament/clusters/reporting/resources/lead-analysis'],
    'activity analysis' => [ActivityAnalysisResource::class, 'crm::filament/clusters/reporting/resources/activity-analysis'],
]);
