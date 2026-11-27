<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\SessionReportBuilder;

class SessionReport extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $slug = 'point-of-sale/session-report';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'point-of-sale::filament.admin.clusters.reporting.pages.session-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->filtersForm->fill();
    }

    protected function getForms(): array
    {
        return ['filtersForm'];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->schema([
                        Select::make('sessionId')
                            ->label(__('point-of-sale::filament/admin/clusters/reporting/pages/session-report.filters.heading'))
                            ->placeholder(__('point-of-sale::filament/admin/clusters/reporting/pages/session-report.filters.choose'))
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => $this->sessionsQuery()
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn (Session $session) => [$session->id => $this->sessionLabel($session)])
                                ->all())
                            ->getSearchResultsUsing(fn (string $search): array => $this->sessionsQuery()
                                ->where(fn (Builder $query) => $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('started_at', 'like', "%{$search}%")
                                    ->orWhereHas('config', fn (Builder $config) => $config->where('name', 'like', "%{$search}%")))
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn (Session $session) => [$session->id => $this->sessionLabel($session)])
                                ->all())
                            ->getOptionLabelUsing(fn ($value): ?string => ($session = Session::query()->with('config')->find($value)) ? $this->sessionLabel($session) : null)
                            ->live(),
                    ]),
            ]);
    }

    protected function sessionsQuery(): Builder
    {
        return Session::query()
            ->with('config')
            ->latest('id');
    }

    protected function sessionLabel(Session $session): string
    {
        return collect([$session->name, $session->config?->name, $session->started_at?->format('M j, Y')])
            ->filter()
            ->implode(' — ');
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_session_report';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.navigation.title');
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.title');
    }

    public function getReport(): ?array
    {
        $sessionId = $this->data['sessionId'] ?? null;

        return $sessionId
            ? app(SessionReportBuilder::class)->build((int) $sessionId)
            : null;
    }
}
