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
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\BalanceSheetExport;

class BalanceSheet extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;

    protected string $view = 'accounting::filament.clusters.reporting.pages.balance-sheet';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Balance Sheet';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_balance_sheet';
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
                    $data = $this->balanceSheetData;
                    $date = $this->parseDateRange() ? Carbon::parse($this->parseDateRange()[1]) : now();

                    return Excel::download(
                        new BalanceSheetExport($data, $date),
                        'balance-sheet-'.$date->format('Y-m-d').'.xlsx'
                    );
                }),
            Action::make('pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function () {
                    $data = $this->balanceSheetData;
                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.balance-sheet', [
                        'data' => $data,
                    ]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'balance-sheet-'.now()->format('Y-m-d').'.pdf');
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
    public function balanceSheetData(): array
    {
        $dateRange = $this->parseDateRange();
        $date = $dateRange ? Carbon::parse($dateRange[1]) : now();

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
            ->whereDate('accounts_account_move_lines.date', '<=', $date)
            ->groupBy('accounts_account_move_lines.account_id');

        if (! empty($journalIds)) {
            $query->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        $balances = $query->get()->keyBy('account_id');

        $accounts = Account::whereIn('account_type', array_merge(
            array_keys(AccountType::assets()),
            array_keys(AccountType::liabilities()),
            array_keys(AccountType::equity())
        ))->get()->keyBy('id');

        $assets = $this->buildAssetSection($accounts, $balances);
        $liabilities = $this->buildLiabilitySection($accounts, $balances);
        $equity = $this->buildEquitySection($accounts, $balances, $date, $companyId, $journalIds);

        return [
            'sections' => [
                [
                    'title'       => 'ASSETS',
                    'subsections' => [
                        [
                            'title'       => 'Current Assets',
                            'accounts'    => $assets['current_assets'],
                            'total_label' => 'Total Current Assets',
                            'total'       => $assets['total_current'],
                        ],
                        [
                            'title'       => 'Fixed Assets',
                            'accounts'    => $assets['fixed_assets'],
                            'total_label' => 'Total Fixed Assets',
                            'total'       => $assets['total_fixed'],
                        ],
                        [
                            'title'       => 'Non-current Assets',
                            'accounts'    => $assets['non_current_assets'],
                            'total_label' => 'Total Non-current Assets',
                            'total'       => $assets['total_non_current'],
                        ],
                    ],
                    'total_label' => 'Total ASSETS',
                    'total'       => $assets['total'],
                ],
                [
                    'title'       => 'LIABILITIES',
                    'subsections' => [
                        [
                            'title'       => 'Current Liabilities',
                            'accounts'    => $liabilities['current_liabilities'],
                            'total_label' => 'Total Current Liabilities',
                            'total'       => $liabilities['total_current'],
                        ],
                        [
                            'title'       => 'Non-current Liabilities',
                            'accounts'    => $liabilities['non_current_liabilities'],
                            'total_label' => 'Total Non-current Liabilities',
                            'total'       => $liabilities['total_non_current'],
                        ],
                    ],
                    'total_label' => 'Total LIABILITIES',
                    'total'       => $liabilities['total'],
                ],
                [
                    'title'       => 'EQUITY',
                    'subsections' => [
                        [
                            'title'    => 'Unallocated Earnings',
                            'accounts' => [
                                [
                                    'code'    => '',
                                    'name'    => 'Current Year Unallocated Earnings',
                                    'balance' => $equity['current_year_earnings'],
                                ],
                                [
                                    'code'    => '',
                                    'name'    => 'Previous Years Unallocated Earnings',
                                    'balance' => $equity['previous_years_earnings'],
                                ],
                            ],
                            'total_label' => 'Total Unallocated Earnings',
                            'total'       => $equity['total_unallocated'],
                        ],
                        [
                            'title'         => 'Retained Earnings',
                            'accounts'      => array_merge($equity['equity_accounts'], $equity['retained_accounts']),
                            'total_label'   => 'Total Retained Earnings',
                            'total'         => $equity['total_equity'] + $equity['total_retained'],
                            'show_if_empty' => false,
                        ],
                    ],
                    'total_label' => 'Total EQUITY',
                    'total'       => $equity['total'],
                ],
            ],
            'grand_total_label' => 'LIABILITIES + EQUITY',
            'grand_total'       => $liabilities['total'] + $equity['total'],
            'date'              => $date,
        ];
    }

    protected function buildAssetSection($accounts, $balances): array
    {
        $currentAssets = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::ASSET_CASH->value,
            AccountType::ASSET_RECEIVABLE->value,
            AccountType::ASSET_CURRENT->value,
            AccountType::ASSET_PREPAYMENTS->value,
        ]);

        $fixedAssets = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::ASSET_FIXED->value,
        ]);

        $nonCurrentAssets = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::ASSET_NON_CURRENT->value,
        ]);

        $totalCurrent = collect($currentAssets)->sum('balance');
        $totalFixed = collect($fixedAssets)->sum('balance');
        $totalNonCurrent = collect($nonCurrentAssets)->sum('balance');

        return [
            'current_assets'     => $currentAssets,
            'fixed_assets'       => $fixedAssets,
            'non_current_assets' => $nonCurrentAssets,
            'total_current'      => $totalCurrent,
            'total_fixed'        => $totalFixed,
            'total_non_current'  => $totalNonCurrent,
            'total'              => $totalCurrent + $totalFixed + $totalNonCurrent,
        ];
    }

    protected function buildLiabilitySection($accounts, $balances): array
    {
        $currentLiabilities = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::LIABILITY_CURRENT->value,
            AccountType::LIABILITY_PAYABLE->value,
            AccountType::LIABILITY_CREDIT_CARD->value,
        ], true);

        $nonCurrentLiabilities = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::LIABILITY_NON_CURRENT->value,
        ], true);

        $totalCurrent = collect($currentLiabilities)->sum('balance');
        $totalNonCurrent = collect($nonCurrentLiabilities)->sum('balance');

        return [
            'current_liabilities'     => $currentLiabilities,
            'non_current_liabilities' => $nonCurrentLiabilities,
            'total_current'           => $totalCurrent,
            'total_non_current'       => $totalNonCurrent,
            'total'                   => $totalCurrent + $totalNonCurrent,
        ];
    }

    protected function buildEquitySection($accounts, $balances, $date, $companyId, $journalIds): array
    {
        $currentYearStart = now()->startOfYear();

        $currentYearEarnings = $this->calculateEarnings($companyId, $currentYearStart, $date, $journalIds);

        $previousYearsEarnings = $this->calculateEarnings($companyId, null, $currentYearStart->copy()->subDay(), $journalIds);

        $equityAccounts = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::EQUITY->value,
        ], true);

        $retainedEarningsAccounts = $this->getAccountsByTypes($accounts, $balances, [
            AccountType::EQUITY_UNAFFECTED->value,
        ], true);

        $totalEquity = collect($equityAccounts)->sum('balance');
        $totalRetained = collect($retainedEarningsAccounts)->sum('balance');

        $totalUnallocated = $currentYearEarnings + $previousYearsEarnings;

        return [
            'current_year_earnings'   => $currentYearEarnings,
            'previous_years_earnings' => $previousYearsEarnings,
            'total_unallocated'       => $totalUnallocated,
            'equity_accounts'         => $equityAccounts,
            'retained_accounts'       => $retainedEarningsAccounts,
            'total_equity'            => $totalEquity,
            'total_retained'          => $totalRetained,
            'total'                   => abs($totalUnallocated) + $totalEquity + $totalRetained,
        ];
    }

    protected function calculateEarnings($companyId, $startDate, $endDate, $journalIds): float
    {
        $query = MoveLine::query()
            ->select(DB::raw('SUM(accounts_account_move_lines.debit - accounts_account_move_lines.credit) as balance'))
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->where('accounts_account_moves.company_id', $companyId)
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->whereIn('accounts_accounts.account_type', array_merge(
                array_keys(AccountType::income()),
                array_keys(AccountType::expenses())
            ))
            ->whereDate('accounts_account_move_lines.date', '<=', $endDate);

        if ($startDate) {
            $query->whereDate('accounts_account_move_lines.date', '>=', $startDate);
        }

        if (! empty($journalIds)) {
            $query->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        $result = $query->first();

        return -1 * ($result->balance ?? 0);
    }

    protected function getAccountsByTypes($accounts, $balances, array $types, bool $flipSign = false): array
    {
        return $accounts->filter(function ($account) use ($types, $balances) {
            $accountType = $account->account_type instanceof AccountType
                ? $account->account_type->value
                : $account->account_type;

            if (! in_array($accountType, $types)) {
                return false;
            }

            return isset($balances[$account->id]);
        })->map(function ($account) use ($balances, $flipSign) {
            $balance = $balances[$account->id]->balance ?? 0;

            return [
                'id'      => $account->id,
                'code'    => $account->code,
                'name'    => $account->name,
                'balance' => $flipSign ? -1 * $balance : $balance,
            ];
        })->sortBy('code')->values()->all();
    }
}
