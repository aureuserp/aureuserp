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
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\ProfitAndLossExport;

class ProfitLoss extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;

    protected string $view = 'accounting::filament.clusters.reporting.pages.profit-loss';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Profit & Loss';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_profit_loss';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Statement Reports';
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
                    $data = $this->profitLossData;
                    $dateFrom = Carbon::parse($data['date_from']);
                    $dateTo = Carbon::parse($data['date_to']);
                    $filename = 'profit-loss-'.$dateFrom->format('Y-m-d').'-to-'.$dateTo->format('Y-m-d').'.xlsx';

                    return Excel::download(new ProfitAndLossExport($data, $dateFrom, $dateTo), $filename);
                }),
            Action::make('pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->profitLossData;
                    $dateFrom = Carbon::parse($data['date_from']);
                    $dateTo = Carbon::parse($data['date_to']);
                    $filename = 'profit-loss-'.$dateFrom->format('Y-m-d').'-to-'.$dateTo->format('Y-m-d').'.pdf';

                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.profit-loss', ['data' => $data]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename);
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
    public function profitLossData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0]) : now()->startOfMonth();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1]) : now()->endOfMonth();

        $companyId = Auth::user()->default_company_id;
        $journalIds = $this->data['journals'] ?? [];

        $query = MoveLine::query()
            ->select([
                'accounts_account_move_lines.account_id',
                DB::raw('SUM(accounts_account_move_lines.debit) as total_debit'),
                DB::raw('SUM(accounts_account_move_lines.credit) as total_credit'),
                DB::raw('SUM(accounts_account_move_lines.balance) as balance'),
            ])
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->where('accounts_account_moves.company_id', $companyId)
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->whereBetween('accounts_account_moves.date', [$dateFrom, $dateTo])
            ->groupBy('accounts_account_move_lines.account_id');

        if (! empty($journalIds)) {
            $query->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        $balances = $query->get()->keyBy('account_id');

        $accounts = Account::whereIn('account_type', array_merge(
            array_keys(AccountType::income()),
            array_keys(AccountType::expenses())
        ))->get()->keyBy('id');

        $revenue = $this->buildRevenueSection($accounts, $balances);
        $expenses = $this->buildExpenseSection($accounts, $balances);
        $netIncome = $revenue['total'] - $expenses['total'];

        return [
            'sections' => [
                [
                    'title'         => 'REVENUE',
                    'accounts'      => $revenue['accounts'],
                    'total_label'   => 'Total Revenue',
                    'total'         => $revenue['total'],
                    'empty_message' => 'No revenue accounts with transactions in this period',
                ],
                [
                    'title'         => 'EXPENSES',
                    'accounts'      => $expenses['accounts'],
                    'total_label'   => 'Total Expenses',
                    'total'         => $expenses['total'],
                    'is_expense'    => true,
                    'empty_message' => 'No expense accounts with transactions in this period',
                ],
            ],
            'net_income' => $netIncome,
            'is_profit'  => $netIncome >= 0,
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
        ];
    }

    protected function buildRevenueSection($accounts, $balances): array
    {
        $incomeAccounts = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::INCOME->value,
            AccountType::INCOME_OTHER->value,
        ]);

        $totalRevenue = collect($incomeAccounts)->sum('balance');

        return [
            'accounts' => $incomeAccounts,
            'total'    => abs($totalRevenue),
        ];
    }

    protected function buildExpenseSection($accounts, $balances): array
    {
        $expenseAccounts = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::EXPENSE->value,
            AccountType::EXPENSE_DIRECT_COST->value,
            AccountType::EXPENSE_DEPRECIATION->value,
        ]);

        $totalExpenses = collect($expenseAccounts)->sum('balance');

        return [
            'accounts' => $expenseAccounts,
            'total'    => abs($totalExpenses),
        ];
    }

    protected function getAccountsByTypes($accounts, $balances, array $types): array
    {
        return $accounts->filter(function ($account) use ($types, $balances) {
            $accountType = $account->account_type instanceof AccountType
                ? $account->account_type->value
                : $account->account_type;

            if (! in_array($accountType, $types)) {
                return false;
            }

            return isset($balances[$account->id]);
        })->map(function ($account) use ($balances) {
            $balance = $balances[$account->id]->balance ?? 0;

            return [
                'id'      => $account->id,
                'code'    => $account->code,
                'name'    => $account->name,
                'balance' => abs($balance),
            ];
        })->sortBy('code')->values()->all();
    }
}
