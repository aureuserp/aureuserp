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
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\TrialBalanceExport;

class TrialBalance extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;

    protected string $view = 'accounting::filament.clusters.reporting.pages.trial-balance';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Trial Balance';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_trial_balance';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Audit Reports';
    }

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label('Export to Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $data = $this->trialBalanceData;

                    return Excel::download(
                        new TrialBalanceExport(
                            $data['accounts'],
                            $data['date_from'],
                            $data['date_to'],
                            $data['totals']
                        ),
                        'trial-balance-'.$data['date_from']->format('Y-m-d').'-'.$data['date_to']->format('Y-m-d').'.xlsx'
                    );
                }),
            Action::make('pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function () {
                    $data = $this->trialBalanceData;

                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.trial-balance', [
                        'data' => $data,
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'trial-balance-'.$data['date_from']->format('Y-m-d').'-'.$data['date_to']->format('Y-m-d').'.pdf');
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
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
                        ->afterStateUpdated(fn () => null),
                    Select::make('journals')
                        ->label('Journals')
                        ->multiple()
                        ->options(fn () => Journal::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn () => null),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    #[Computed]
    public function trialBalanceData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0]) : now()->startOfMonth();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1]) : now()->endOfMonth();

        $companyId = Auth::user()->default_company_id;
        $journalIds = $this->data['journals'] ?? [];

        $accountsQuery = Account::select(
            'accounts_accounts.id',
            'accounts_accounts.code',
            'accounts_accounts.name',
            'accounts_accounts.account_type',
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date < ? THEN accounts_account_move_lines.debit ELSE 0 END), 0) as initial_debit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date < ? THEN accounts_account_move_lines.credit ELSE 0 END), 0) as initial_credit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date BETWEEN ? AND ? THEN accounts_account_move_lines.debit ELSE 0 END), 0) as period_debit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date BETWEEN ? AND ? THEN accounts_account_move_lines.credit ELSE 0 END), 0) as period_credit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN accounts_account_move_lines.debit ELSE 0 END), 0) as end_debit'),
            DB::raw('COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN accounts_account_move_lines.credit ELSE 0 END), 0) as end_credit')
        )
            ->leftJoin('accounts_account_move_lines', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->leftJoin('accounts_account_moves', function ($join) use ($companyId) {
                $join->on('accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
                    ->where('accounts_account_moves.state', MoveState::POSTED)
                    ->where('accounts_account_moves.company_id', $companyId);
            })
            ->addBinding([$dateFrom, $dateFrom, $dateFrom, $dateTo, $dateFrom, $dateTo, $dateTo, $dateTo], 'select')
            ->groupBy('accounts_accounts.id', 'accounts_accounts.code', 'accounts_accounts.name', 'accounts_accounts.account_type')
            ->havingRaw('(COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN accounts_account_move_lines.debit ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN accounts_account_moves.date <= ? THEN accounts_account_move_lines.credit ELSE 0 END), 0)) > 0', [$dateTo, $dateTo])
            ->orderBy('accounts_accounts.code');

        if (! empty($journalIds)) {
            $accountsQuery->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        $accounts = $accountsQuery->get();

        return [
            'accounts'  => $accounts,
            'totals'    => [
                'initial_debit'  => $accounts->sum('initial_debit'),
                'initial_credit' => $accounts->sum('initial_credit'),
                'period_debit'   => $accounts->sum('period_debit'),
                'period_credit'  => $accounts->sum('period_credit'),
                'end_debit'      => $accounts->sum('end_debit'),
                'end_credit'     => $accounts->sum('end_credit'),
            ],
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];
    }
}
