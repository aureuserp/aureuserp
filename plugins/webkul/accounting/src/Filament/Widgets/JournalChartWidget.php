<?php

namespace Webkul\Accounting\Filament\Widgets;

use Illuminate\Support\Carbon;
use Livewire\Component;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;

class JournalChartWidget extends Component
{
    public ?object $journal = null;

    public function mount($journal)
    {
        $this->journal = $journal;
    }

    public function getDashboardData(): array
    {
        $type = $this->journal->type;
        $baseQuery = Move::where('journal_id', $this->journal->id);

        $data = [
            'stats'   => [],
            'checks'  => [],
            'actions' => [],
            'graph'   => [],
        ];

        if ($type === JournalType::SALE) {
            $data['stats'] = [
                'to_validate' => [
                    'label'            => 'To Validate',
                    'url'              => $this->getUrl('index', ['activeTableView' => 'draft']),
                    'value'            => (clone $baseQuery)->where('state', MoveState::DRAFT)->count(),
                    'amount'           => $amount = (clone $baseQuery)->where('state', MoveState::DRAFT)->sum('amount_total'),
                    'formatted_amount' => money($amount),
                ],
                'unpaid' => [
                    'label' => 'Unpaid',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'unpaid']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('amount_residual', '>', 0)
                        ->whereNotIn('payment_state', [PaymentState::PAID, PaymentState::IN_PAYMENT])
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('amount_residual', '>', 0)
                        ->whereNotIn('payment_state', [PaymentState::PAID, PaymentState::IN_PAYMENT])
                        ->sum('amount_residual'),
                    'formatted_amount' => money($amount),
                ],
                'late' => [
                    'label' => 'Late',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'overdue']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', today())
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', today())
                        ->sum('amount_residual'),
                    'formatted_amount' => money($amount),
                ],
                'to_pay' => [
                    'label' => 'To Pay',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'to_pay']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL])
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL])
                        ->sum('amount_residual'),
                    'formatted_amount' => money($amount),
                ],
            ];
        } elseif ($type === JournalType::PURCHASE) {
            $data['stats'] = [
                'to_validate' => [
                    'label'            => 'To Validate',
                    'url'              => $this->getUrl('index', ['activeTableView' => 'draft']),
                    'value'            => (clone $baseQuery)->where('state', MoveState::DRAFT)->count(),
                    'amount'           => $amount = (clone $baseQuery)->where('state', MoveState::DRAFT)->sum('amount_total'),
                    'formatted_amount' => money($amount),
                ],
                'late' => [
                    'label' => 'Late',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'overdue']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', today())
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', today())
                        ->sum('amount_residual'),
                    'formatted_amount' => money($amount),
                ],
                'to_pay' => [
                    'label' => 'To Pay',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'to_pay']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL])
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL])
                        ->sum('amount_residual'),
                    'formatted_amount' => money($amount),
                ],
            ];
        } elseif ($type === JournalType::GENERAL) {
            $data['stats'] = [
                'entries_count' => [
                    'label' => 'Entries',
                    'value' => (clone $baseQuery)->count(),
                ],
            ];
        } else {
            $data['stats'] = [
                'payments' => [
                    'label'  => 'Payments',
                    'url'    => $this->getUrl('index'),
                    'value'  => null,
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->sum('amount_total'),
                    'formatted_amount' => money($amount),
                ],
            ];
        }

        $data['actions'] = $this->getActions();
        $data['graph'] = $this->getChartData();

        return $data;
    }

    private function getUrl(string $name, array $parameters = []): ?string
    {
        return match ($this->journal->type) {
            JournalType::SALE     => InvoiceResource::getUrl($name, $parameters),
            JournalType::PURCHASE => BillResource::getUrl($name, $parameters),
            JournalType::GENERAL  => JournalEntryResource::getUrl($name, $parameters),
            default               => PaymentResource::getUrl($name, $parameters),
        };
    }

    private function getActions(): array
    {
        return match ($this->journal->type) {
            JournalType::GENERAL  => [['label' => 'New Entry',   'url' => $this->getUrl('create')]],
            JournalType::SALE     => [['label' => 'New Invoice', 'url' => $this->getUrl('create')]],
            JournalType::PURCHASE => [['label' => 'New Bill',    'url' => $this->getUrl('create')]],
            default               => [['label' => 'New Payment', 'url' => $this->getUrl('create')]],
        };
    }

    public function getChartData(): array
    {
        return $this->isLiquidityJournal()
            ? $this->getLiquidityChartData()
            : $this->getInvoiceChartData();
    }

    private function isLiquidityJournal(): bool
    {
        return in_array($this->journal->type, [
            JournalType::BANK,
            JournalType::CASH,
            JournalType::CREDIT_CARD,
        ]);
    }

    private function getLiquidityChartData(): array
    {
        $start = now()->subWeeks(5)->startOfWeek();
        $end = now()->endOfWeek();

        $moves = Move::query()
            ->where('journal_id', $this->journal->id)
            ->where('state', MoveState::POSTED)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $labels = [];
        $balances = [];
        $runningBalance = 0;

        foreach ($moves->groupBy(fn ($m) => Carbon::parse($m->date)->format('Y-m-d')) as $week => $weekMoves) {
            $labels[] = Carbon::parse($week)->format('d M');

            foreach ($weekMoves as $move) {
                $runningBalance += (float) $move->amount_total;
            }

            $balances[] = $runningBalance;
        }

        return [
            'type'     => 'line',
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'       => 'Balance',
                    'data'        => $balances,
                    'borderColor' => '#3b82f6',
                    'tension'     => 0.3,
                    'fill'        => false,
                ],
            ],
        ];
    }

    private function getInvoiceChartData(): array
    {
        $now = now();

        $thisWeekStart = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $thisWeekEnd = $now->copy()->endOfWeek(Carbon::SATURDAY);

        $prevWeekStart = $now->copy()->subWeek()->startOfWeek(Carbon::SUNDAY);
        $prevWeekEnd = $now->copy()->subWeek()->endOfWeek(Carbon::SATURDAY);

        $nextWeekStart = $now->copy()->addWeek()->startOfWeek(Carbon::SUNDAY);
        $nextWeekEnd = $now->copy()->addWeek()->endOfWeek(Carbon::SATURDAY);

        $futureWeekStart = $now->copy()->addWeeks(2)->startOfWeek(Carbon::SUNDAY);
        $futureWeekEnd = $now->copy()->addWeeks(2)->endOfWeek(Carbon::SATURDAY);

        $labels = [
            'Overdue',
            $prevWeekStart->format('d M').' - '.$prevWeekEnd->format('d M'),
            'This Week',
            $nextWeekStart->format('d M').' - '.$nextWeekEnd->format('d M'),
            $futureWeekStart->format('d M').' - '.$futureWeekEnd->format('d M'),
            'Not Due',
        ];

        $late = array_fill(0, 6, 0);
        $onTime = array_fill(0, 6, 0);

        $moves = Move::query()
            ->where('journal_id', $this->journal->id)
            ->where('state', MoveState::POSTED)
            ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL])
            ->where('amount_residual', '>', 0)
            ->get();

        foreach ($moves as $move) {
            $residual = (float) $move->amount_residual;
            $due = Carbon::parse($move->invoice_date_due);
            $isLate = $due->lt(today());

            if ($due->lt(today())) {
                $late[0] += $residual;
            } elseif ($due->between($prevWeekStart, $prevWeekEnd)) {
                $isLate ? $late[1] += $residual : $onTime[1] += $residual;
            } elseif ($due->between($thisWeekStart, $thisWeekEnd)) {
                $isLate ? $late[2] += $residual : $onTime[2] += $residual;
            } elseif ($due->between($nextWeekStart, $nextWeekEnd)) {
                $isLate ? $late[3] += $residual : $onTime[3] += $residual;
            } elseif ($due->between($futureWeekStart, $futureWeekEnd)) {
                $isLate ? $late[4] += $residual : $onTime[4] += $residual;
            } else {
                $onTime[5] += $residual;
            }
        }

        return [
            'type'     => 'bar',
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Overdue',
                    'data'            => $late,
                    'backgroundColor' => '#ef4444',
                ], [
                    'label'           => 'On Time',
                    'data'            => $onTime,
                    'backgroundColor' => '#22c55e',
                ],
            ],
        ];
    }

    public function render()
    {
        return view('accounting::filament.widgets.journal-chart-widget', [
            'dashboard' => $this->getDashboardData(),
        ]);
    }
}
