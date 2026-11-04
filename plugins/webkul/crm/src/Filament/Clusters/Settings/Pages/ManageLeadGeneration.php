<?php

namespace Webkul\Crm\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Services\VisitsToLeads;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageLeadGeneration extends SettingsPage
{
    use HasCRMClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    protected static ?string $slug = 'crm/manage-lead-generation';

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 3;

    protected static string $settings = CRMSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_lead_generation';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('crm::filament/clusters/settings/pages/manage-lead-generation.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-generation.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-generation.description');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-generation.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('visits_to_leads')
                    ->label(__('crm::filament/clusters/settings/pages/manage-lead-generation.form.visits-to-leads'))
                    ->helperText(fn (): string => VisitsToLeads::websiteAvailable() ? __('crm::filament/clusters/settings/pages/manage-lead-generation.form.helper') : __('crm::filament/clusters/settings/pages/manage-lead-generation.form.website-missing'))
                    ->disabled(fn (): bool => ! VisitsToLeads::websiteAvailable())
                    ->live(),
                Text::make(fn (): string => static::activeRevealRuleCount() > 0
                    ? __('crm::filament/clusters/settings/pages/manage-lead-generation.form.active-rules', ['count' => static::activeRevealRuleCount()])
                    : __('crm::filament/clusters/settings/pages/manage-lead-generation.form.no-rules'))
                    ->color(fn (): string => static::activeRevealRuleCount() > 0 ? 'success' : 'warning')
                    ->weight('medium')
                    ->visible(fn (Get $get): bool => (bool) $get('visits_to_leads')),
                Actions::make([
                    Action::make('manageRevealRules')
                        ->label(__('crm::filament/clusters/settings/pages/manage-lead-generation.form.manage-rules'))
                        ->icon('heroicon-o-cursor-arrow-ripple')
                        ->color('gray')
                        ->url(fn (): string => RevealRuleResource::getUrl('index'))
                        ->visible(fn (): bool => RevealRuleResource::canViewAny()),
                    Action::make('generateLeadsForExistingVisitors')
                        ->label(__('crm::filament/clusters/settings/pages/manage-lead-generation.form.generate-existing'))
                        ->icon('heroicon-o-user-plus')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalDescription(fn (): string => __('crm::filament/clusters/settings/pages/manage-lead-generation.form.generate-help', ['count' => static::visitorsWithoutLeadCount()]))
                        ->visible(fn (Get $get): bool => (bool) $get('visits_to_leads') && VisitsToLeads::enabled() && static::activeRevealRuleCount() > 0 && static::visitorsWithoutLeadCount() > 0)
                        ->action(function (): void {
                            $count = app(VisitsToLeads::class)->generateForExistingVisitors();

                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/settings/pages/manage-lead-generation.form.generated', ['count' => $count]))
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function activeRevealRuleCount(): int
    {
        return once(fn (): int => RevealRule::query()->where('is_active', true)->count());
    }

    public static function visitorsWithoutLeadCount(): int
    {
        return (int) app(VisitsToLeads::class)->visitorsWithoutLeadQuery()?->count();
    }
}
