<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\AgedReceivableExport;
use Webkul\Partner\Models\Partner;

class AgedReceivable extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected string $view = 'accounting::filament.clusters.reporting.pages.aged-receivable';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Aged Receivable';

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public array $expandedPartners = [];

    public array $partnerLines = [];

    public ?int $loadingPartnerId = null;

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_aged_receivable';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Partner Reports';
    }

    public function mount(): void
    {
        $this->form->fill([
            'as_of_date' => now()->toDateString(),
            'basis'      => 'due_date',
            'period'     => 30,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $data = $this->agedReceivableData;
                    $partners = $data['partners'];
                    $asOfDate = $data['as_of_date']->toDateString();
                    $period = $data['period'];
                    $state = $this->form->getState();
                    $basis = $state['basis'] ?? 'due_date';

                    foreach ($this->expandedPartners as $partnerId) {
                        if (isset($partners[$partnerId])) {
                            $partners[$partnerId]['lines'] = $this->getPartnerLines($partnerId);
                        }
                    }

                    return Excel::download(
                        new AgedReceivableExport($partners, $asOfDate, $period, $basis, $this->expandedPartners),
                        'aged-receivable-'.$asOfDate.'.xlsx'
                    );
                }),

            Action::make('pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(function () {
                    $data = $this->agedReceivableData;
                    $partners = $data['partners'];
                    $asOfDate = $data['as_of_date']->toDateString();
                    $period = $data['period'];

                    $partnerLines = [];

                    foreach ($this->expandedPartners as $partnerId) {
                        if (isset($partners[$partnerId])) {
                            $partnerLines[$partnerId] = $this->getPartnerLines($partnerId);
                        }
                    }

                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.aged-receivable', [
                        'partners'         => $partners,
                        'asOfDate'         => $asOfDate,
                        'period'           => $period,
                        'expandedPartners' => $this->expandedPartners,
                        'partnerLines'     => $partnerLines,
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'aged-receivable-'.$asOfDate.'.pdf');
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
                    DatePicker::make('as_of_date')
                        ->label('As of')
                        ->default(now())
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('basis')
                        ->label('Based on')
                        ->options([
                            'due_date'     => 'Due Date',
                            'invoice_date' => 'Invoice Date',
                        ])
                        ->default('due_date')
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('period')
                        ->label('Period Length (days)')
                        ->options([
                            30 => '30 Days',
                            60 => '60 Days',
                            90 => '90 Days',
                        ])
                        ->default(30)
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('journals')
                        ->label('Journals')
                        ->multiple()
                        ->options(Journal::pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('partners')
                        ->label('Partners')
                        ->multiple()
                        ->options(Partner::pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetExpandedState()),

                    Select::make('posted_entries')
                        ->label('Entries')
                        ->options([
                            'posted' => 'Posted Entries',
                            'all'    => 'All Entries',
                        ])
                        ->default('posted')
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

    public function togglePartnerLines($partnerId): void
    {
        if (in_array($partnerId, $this->expandedPartners)) {
            $this->expandedPartners = array_values(array_diff($this->expandedPartners, [$partnerId]));
        } else {
            $this->expandedPartners[] = $partnerId;

            if (! isset($this->partnerLines[$partnerId])) {
                $this->partnerLines[$partnerId] = $this->fetchPartnerLines($partnerId);
            }
        }
    }

    public function isPartnerExpanded($partnerId): bool
    {
        return in_array($partnerId, $this->expandedPartners);
    }

    public function expandAll(): void
    {
        $data = $this->agedReceivableData;
        $this->expandedPartners = array_keys($data['partners']);

        foreach ($this->expandedPartners as $partnerId) {
            if (! isset($this->partnerLines[$partnerId])) {
                $this->partnerLines[$partnerId] = $this->fetchPartnerLines($partnerId);
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
        $this->partnerLines = [];
    }

    public function getPartnerLines($partnerId): array
    {
        if (! isset($this->partnerLines[$partnerId])) {
            $this->partnerLines[$partnerId] = $this->fetchPartnerLines($partnerId);
        }

        return $this->partnerLines[$partnerId];
    }

    protected function fetchPartnerLines($partnerId): array
    {
        $state = $this->form->getState();
        $asOfDate = Carbon::parse($state['as_of_date'] ?? now());
        $basis = $state['basis'] ?? 'due_date';
        $period = $state['period'] ?? 30;
        $companyId = Auth::user()->default_company_id;
        $postedOnly = ($state['posted_entries'] ?? 'posted') === 'posted';

        $query = MoveLine::select(
            'accounts_account_moves.name as move_name',
            'accounts_account_moves.invoice_date',
            'accounts_account_moves.invoice_date_due',
            'accounts_account_moves.reference',
            'accounts_journals.name as journal_name',
            'accounts_account_move_lines.amount_residual'
        )
            ->join('accounts_account_moves', 'accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
            ->join('accounts_accounts', 'accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
            ->leftJoin('accounts_journals', 'accounts_account_moves.journal_id', '=', 'accounts_journals.id')
            ->where('accounts_accounts.account_type', AccountType::ASSET_RECEIVABLE)
            ->where('accounts_account_moves.company_id', $companyId)
            ->where('accounts_account_move_lines.amount_residual', '!=', 0)
            ->where('accounts_account_move_lines.partner_id', $partnerId)
            ->orderBy('accounts_account_moves.invoice_date');

        if ($postedOnly) {
            $query->where('accounts_account_moves.state', MoveState::POSTED);
        }

        $moveLines = $query->get();
        $lines = [];

        foreach ($moveLines as $line) {
            $referenceDate = $basis === 'due_date'
                ? Carbon::parse($line->invoice_date_due)
                : Carbon::parse($line->invoice_date);

            $daysOverdue = $referenceDate->diffInDays($asOfDate, false);
            $amount = (float) $line->amount_residual;

            $lineData = [
                'move_name'        => $line->move_name,
                'invoice_date'     => $line->invoice_date,
                'invoice_date_due' => $line->invoice_date_due,
                'reference'        => $line->reference,
                'journal_name'     => $line->journal_name,
                'days_overdue'     => $daysOverdue,
                'at_date'          => 0,
                'period_1'         => 0,
                'period_2'         => 0,
                'period_3'         => 0,
                'period_4'         => 0,
                'older'            => 0,
            ];

            if ($daysOverdue < 0) {
                $lineData['at_date'] = $amount;
            } elseif ($daysOverdue <= $period) {
                $lineData['period_1'] = $amount;
            } elseif ($daysOverdue <= $period * 2) {
                $lineData['period_2'] = $amount;
            } elseif ($daysOverdue <= $period * 3) {
                $lineData['period_3'] = $amount;
            } elseif ($daysOverdue <= $period * 4) {
                $lineData['period_4'] = $amount;
            } else {
                $lineData['older'] = $amount;
            }

            $lines[] = $lineData;
        }

        return $lines;
    }

    #[Computed]
    public function agedReceivableData(): array
    {
        $state = $this->form->getState();
        $asOfDate = Carbon::parse($state['as_of_date'] ?? now());
        $basis = $state['basis'] ?? 'due_date';
        $period = $state['period'] ?? 30;
        $journalIds = $state['journals'] ?? [];
        $partnerIds = $state['partners'] ?? [];
        $postedOnly = ($state['posted_entries'] ?? 'posted') === 'posted';
        $companyId = Auth::user()->default_company_id;

        $query = MoveLine::select(
            'accounts_account_move_lines.*',
            'accounts_account_moves.name as move_name',
            'accounts_account_moves.invoice_date',
            'accounts_account_moves.invoice_date_due',
            'accounts_account_moves.reference',
            'accounts_account_moves.state',
            'accounts_journals.name as journal_name',
            'partners_partners.name as partner_name',
            'partners_partners.id as partner_id'
        )
            ->join('accounts_account_moves', 'accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
            ->join('accounts_accounts', 'accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
            ->leftJoin('accounts_journals', 'accounts_account_moves.journal_id', '=', 'accounts_journals.id')
            ->leftJoin('partners_partners', 'accounts_account_move_lines.partner_id', '=', 'partners_partners.id')
            ->where('accounts_accounts.account_type', AccountType::ASSET_RECEIVABLE)
            ->where('accounts_account_moves.company_id', $companyId)
            ->where('accounts_account_move_lines.amount_residual', '!=', 0)
            ->whereNotNull('accounts_account_move_lines.partner_id');

        if ($postedOnly) {
            $query->where('accounts_account_moves.state', MoveState::POSTED);
        }

        if (! empty($journalIds)) {
            $query->whereIn('accounts_account_moves.journal_id', $journalIds);
        }

        if (! empty($partnerIds)) {
            $query->whereIn('accounts_account_move_lines.partner_id', $partnerIds);
        }

        $moveLines = $query->orderBy('partners_partners.name')
            ->orderBy('accounts_account_moves.invoice_date')
            ->get();

        $partnerData = [];

        foreach ($moveLines as $line) {
            $partnerId = $line->partner_id;

            if (! isset($partnerData[$partnerId])) {
                $partnerData[$partnerId] = [
                    'id'           => $partnerId,
                    'partner_name' => $line->partner_name,
                    'at_date'      => 0,
                    'period_1'     => 0,
                    'period_2'     => 0,
                    'period_3'     => 0,
                    'period_4'     => 0,
                    'older'        => 0,
                    'total'        => 0,
                ];
            }

            $referenceDate = $basis === 'due_date'
                ? Carbon::parse($line->invoice_date_due)
                : Carbon::parse($line->invoice_date);

            $daysOverdue = $referenceDate->diffInDays($asOfDate, false);
            $amount = (float) $line->amount_residual;

            if ($daysOverdue < 0) {
                $partnerData[$partnerId]['at_date'] += $amount;
            } elseif ($daysOverdue <= $period) {
                $partnerData[$partnerId]['period_1'] += $amount;
            } elseif ($daysOverdue <= $period * 2) {
                $partnerData[$partnerId]['period_2'] += $amount;
            } elseif ($daysOverdue <= $period * 3) {
                $partnerData[$partnerId]['period_3'] += $amount;
            } elseif ($daysOverdue <= $period * 4) {
                $partnerData[$partnerId]['period_4'] += $amount;
            } else {
                $partnerData[$partnerId]['older'] += $amount;
            }

            $partnerData[$partnerId]['total'] += $amount;
        }

        $partnerData = array_filter($partnerData, fn ($partner) => abs($partner['total']) > 0.01);

        $hasUnposted = MoveLine::join('accounts_account_moves', 'accounts_account_move_lines.move_id', '=', 'accounts_account_moves.id')
            ->join('accounts_accounts', 'accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
            ->where('accounts_accounts.account_type', AccountType::ASSET_RECEIVABLE)
            ->where('accounts_account_moves.company_id', $companyId)
            ->where('accounts_account_moves.state', '!=', MoveState::POSTED)
            ->exists();

        return [
            'partners'     => $partnerData,
            'as_of_date'   => $asOfDate,
            'period'       => $period,
            'has_unposted' => $hasUnposted,
        ];
    }
}
