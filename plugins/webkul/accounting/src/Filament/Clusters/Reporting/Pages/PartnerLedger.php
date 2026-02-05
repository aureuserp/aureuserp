<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\PartnerLedgerExport;
use Webkul\Partner\Models\Partner;

class PartnerLedger extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;

    protected string $view = 'accounting::filament.clusters.reporting.pages.partner-ledger';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Partner Ledger';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public array $expandedPartners = [];

    public array $loadedMoveLines = [];

    public ?int $loadingPartnerId = null;

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_partner_ledger';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Partner Reports';
    }

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $data = $this->partnerLedgerData;
                    $partners = $data['partners'];
                    $dateFrom = $data['date_from'];
                    $dateTo = $data['date_to'];

                    foreach ($partners as &$partner) {
                        $partner['moves'] = $this->getPartnerMoves($partner['id']);
                    }

                    return Excel::download(
                        new PartnerLedgerExport($partners, $dateFrom, $dateTo, fn ($id) => $this->getPartnerMoves($id), $this->expandedPartners),
                        'partner-ledger-'.$dateFrom.'-to-'.$dateTo.'.xlsx'
                    );
                }),

            Action::make('pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(function () {
                    $data = $this->partnerLedgerData;
                    $getPartnerMoves = fn ($partnerId) => $this->getPartnerMoves($partnerId);

                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.partner-ledger', [
                        'data'             => $data,
                        'getPartnerMoves'  => $getPartnerMoves,
                        'expandedPartners' => $this->expandedPartners,
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'partner-ledger-'.$data['date_from'].'-to-'.$data['date_to'].'.pdf');
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns([
                    'default' => 1,
                    'sm'      => 3,
                ])
                ->schema([
                    DateRangePicker::make('date_range')
                        ->label('Date Range')
                        ->suffixIcon('heroicon-o-calendar')
                        ->defaultThisMonth()
                        ->ranges([
                            'Today'        => [now()->startOfDay(), now()->endOfDay()],
                            'Yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
                            'This Month'   => [now()->startOfMonth(), now()->endOfMonth()],
                            'Last Month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
                            'This Quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
                            'Last Quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
                            'This Year'    => [now()->startOfYear(), now()->endOfYear()],
                            'Last Year'    => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
                        ])
                        ->alwaysShowCalendar()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('partners')
                        ->label('Partners')
                        ->multiple()
                        ->options(Partner::pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('journals')
                        ->label('Journals')
                        ->multiple()
                        ->options(Journal::pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    #[Computed]
    public function partnerLedgerData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0]) : now()->startOfYear();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1]) : now();

        $partnerIds = $this->form->getState()['partners'] ?? [];
        $journalIds = $this->form->getState()['journals'] ?? [];
        $companyId = Auth::user()->default_company_id;

        $partnersQuery = Partner::select(
            'partners_partners.id',
            'partners_partners.name',
            'partners_partners.email',
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date < ? THEN accounts_account_move_lines.balance ELSE 0 END), 0) as opening_balance'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date BETWEEN ? AND ? THEN accounts_account_move_lines.debit ELSE 0 END), 0) as period_debit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date BETWEEN ? AND ? THEN accounts_account_move_lines.credit ELSE 0 END), 0) as period_credit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN accounts_account_move_lines.balance ELSE 0 END), 0) as ending_balance')
        )
            ->join('accounts_account_move_lines', 'partners_partners.id', '=', 'accounts_account_move_lines.partner_id')
            ->join('accounts_account_moves', function ($join) use ($companyId) {
                $join->on('accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
                    ->where('accounts_account_moves.state', MoveState::POSTED)
                    ->where('accounts_account_moves.company_id', $companyId);
            })
            ->addBinding([$dateFrom, $dateFrom, $dateTo, $dateFrom, $dateTo, $dateTo], 'select')
            ->groupBy('partners_partners.id', 'partners_partners.name', 'partners_partners.email')
            ->havingRaw('(COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN ABS(accounts_account_move_lines.debit) ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN ABS(accounts_account_move_lines.credit) ELSE 0 END), 0)) > 0', [$dateTo, $dateTo])
            ->orderBy('partners_partners.name');

        if (! empty($partnerIds)) {
            $partnersQuery->whereIn('partners_partners.id', $partnerIds);
        }

        if (! empty($journalIds)) {
            $partnersQuery->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        return [
            'partners'  => $partnersQuery->get(),
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];
    }

    public function togglePartnerLines($partnerId): void
    {
        if (in_array($partnerId, $this->expandedPartners)) {
            $this->expandedPartners = array_values(array_diff($this->expandedPartners, [$partnerId]));
        } else {
            $this->expandedPartners[] = $partnerId;

            if (! isset($this->loadedMoveLines[$partnerId])) {
                $this->loadedMoveLines[$partnerId] = $this->fetchPartnerMoves($partnerId);
            }
        }
    }

    public function isPartnerExpanded($partnerId): bool
    {
        return in_array($partnerId, $this->expandedPartners);
    }

    public function expandAll(): void
    {
        $data = $this->partnerLedgerData;
        $this->expandedPartners = $data['partners']->pluck('id')->toArray();

        foreach ($this->expandedPartners as $partnerId) {
            if (! isset($this->loadedMoveLines[$partnerId])) {
                $this->loadedMoveLines[$partnerId] = $this->fetchPartnerMoves($partnerId);
            }
        }
    }

    public function collapseAll(): void
    {
        $this->expandedPartners = [];
    }

    public function resetExpandedState(): void
    {
        $this->expandedPartners = [];
        $this->loadedMoveLines = [];
    }

    public function getPartnerMoves($partnerId): array
    {
        if (! isset($this->loadedMoveLines[$partnerId])) {
            $this->loadedMoveLines[$partnerId] = $this->fetchPartnerMoves($partnerId);
        }

        return $this->loadedMoveLines[$partnerId];
    }

    protected function fetchPartnerMoves($partnerId): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0]) : now()->startOfYear();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1]) : now();
        $journalIds = $this->form->getState()['journals'] ?? [];
        $companyId = Auth::user()->default_company_id;

        $query = MoveLine::select(
            'accounts_account_move_lines.*',
            'accounts_account_moves.name as move_name',
            'accounts_account_moves.invoice_date',
            'accounts_account_moves.invoice_date_due',
            'accounts_account_moves.reference as ref',
            'accounts_journals.name as journal_name',
            'accounts_accounts.code as account_code',
            'accounts_accounts.name as account_name'
        )
            ->join('accounts_account_moves', 'accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
            ->leftJoin('accounts_journals', 'accounts_account_moves.journal_id', '=', 'accounts_journals.id')
            ->leftJoin('accounts_accounts', 'accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
            ->where('accounts_account_move_lines.partner_id', $partnerId)
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->where('accounts_account_moves.company_id', $companyId)
            ->whereBetween('accounts_account_moves.date', [$dateFrom, $dateTo])
            ->orderBy('accounts_account_moves.date')
            ->orderBy('accounts_account_moves.id');

        if (! empty($journalIds)) {
            $query->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        return $query->get()->toArray();
    }
}
