<?php

namespace Webkul\Accounting\Filament\Widgets;

use Illuminate\Support\Carbon;
use Livewire\Component;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentResource;


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
            'stats' => [],
            'checks' => [],
            'actions' => [],
            'graph' => [],
        ];

        if ($type === JournalType::SALE) {
            $data['stats'] = [
                'to_validate' => [
                    'label' => 'To Validate',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'draft']),
                    'value' => (clone $baseQuery)->where('state', MoveState::DRAFT)->count(),
                    'amount' => $amount = (clone $baseQuery)->where('state', MoveState::DRAFT)->sum('amount_total'),
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
                        ->sum('amount_total'),
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
                    'label' => 'To Validate',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'draft']),
                    'value' => (clone $baseQuery)->where('state', MoveState::DRAFT)->count(),
                    'amount' => $amount = (clone $baseQuery)->where('state', MoveState::DRAFT)->sum('amount_total'),
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
        } elseif ($type == JournalType::GENERAL) {
            $data['stats'] = [
                'entries_count' => [
                    'label' => 'Entries',
                    'value' => (clone $baseQuery)->count(),
                ],
            ];
        } else {
            $data['stats'] = [
                'payments' => [
                    'label' => 'Payments',
                    'url'   => $this->getUrl('index', [
                        'filters[queryBuilder][rules][89kL][type]' => 'journal',
                        'filters[queryBuilder][rules][89kL][data][operator]' => 'isRelatedTo',
                        'filters[queryBuilder][rules][89kL][data][settings][values][0]' => $this->journal->id,
                    ]),
                    'value' => null,
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
        if ($this->journal->type == JournalType::SALE) {
            return InvoiceResource::getUrl($name, $parameters);
        } elseif ($this->journal->type == JournalType::PURCHASE) {
            return BillResource::getUrl($name, $parameters);
        } elseif ($this->journal->type == JournalType::GENERAL) {
            return JournalEntryResource::getUrl($name, $parameters);
        } else {
            return PaymentResource::getUrl($name, $parameters);
        }
    }

    private function getActions(): array
    {
        $type = $this->journal->type;
        $actions = [];
        if ($type == JournalType::GENERAL) {
            $actions[] = [
                'label' => 'New Entry',
                'url' => $this->getUrl('create'),
            ];
        } elseif ($type == JournalType::SALE) {
            $actions[] = [
                'label' => 'New Invoice',
                'url' => $this->getUrl('create'),
            ];
        } elseif ($type == JournalType::PURCHASE) {
            $actions[] = [
                'label' => 'New Bill',
                'url' => $this->getUrl('create'),
            ];
        } else {
            $actions[] = [
                'label' => 'New Payment',
                'url' => $this->getUrl('create'),
            ];
        }
        return $actions;
    }

    public function getChartData(): array
    {
        $now = Carbon::now();
        
        $thisWeekStart = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $thisWeekEnd = $now->copy()->endOfWeek(Carbon::SATURDAY);
        
        $prevWeekStart = $thisWeekStart->copy()->subWeek();
        $prevWeekEnd = $thisWeekEnd->copy()->subWeek();
        
        $nextWeekStart = $thisWeekStart->copy()->addWeek();
        $nextWeekEnd = $thisWeekEnd->copy()->addWeek();
        
        $nextToNextWeekStart = $thisWeekStart->copy()->addWeeks(2);
        $nextToNextWeekEnd = $thisWeekEnd->copy()->addWeeks(2);

        $labels = [
            'Due',
            $prevWeekStart->format('d M') . ' - ' . $prevWeekEnd->format('d M'),
            'This Week',
            $nextWeekStart->format('d M') . ' - ' . $nextWeekEnd->format('d M'),
            $nextToNextWeekStart->format('d M') . ' - ' . $nextToNextWeekEnd->format('d M'),
            'Not Due',
        ];

        $invoices = Move::query()
            ->where('journal_id', $this->journal->id)
            ->where('state', 'posted')
            ->whereIn('payment_state', ['not_paid', 'partial'])
            ->where('amount_residual', '>', 0)
            ->get();

        $lateData = [0, 0, 0, 0, 0, 0];
        $onTimeData = [0, 0, 0, 0, 0, 0];

        $today = $now->copy()->startOfDay();
        
        foreach ($invoices as $invoice) {
            $dueDate = Carbon::parse($invoice->invoice_date_due);
            $residual = (float) $invoice->amount_residual;
            $isLate = $dueDate->lt($today);

            if ($isLate) {
                $lateData[0] += $residual;
            }
            
            if (!$isLate) {
                $onTimeData[5] += $residual;
            }

            if ($dueDate->between($prevWeekStart, $prevWeekEnd, true)) {
                if ($isLate) {
                    $lateData[1] += $residual;
                } else {
                    $onTimeData[1] += $residual;
                }
            } elseif ($dueDate->between($thisWeekStart, $thisWeekEnd, true)) {
                if ($isLate) {
                    $lateData[2] += $residual;
                } else {
                    $onTimeData[2] += $residual;
                }
            } elseif ($dueDate->between($nextWeekStart, $nextWeekEnd, true)) {
                $onTimeData[3] += $residual;
            } elseif ($dueDate->between($nextToNextWeekStart, $nextToNextWeekEnd, true)) {
                $onTimeData[4] += $residual;
            }
        }

        $type = $this->journal->type;
        $graphType = in_array($type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]) ? 'line' : 'bar';
        
        return [
            'type'     => $graphType,
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Overdue',
                    'data'            => $lateData,
                    'backgroundColor' => '#ef4444',
                    'borderColor'     => '#ef4444',
                    'stack'           => 'stack0',
                ],
                [
                    'label'           => 'On Time',
                    'data'            => $onTimeData,
                    'backgroundColor' => '#22c55e',
                    'borderColor'     => '#22c55e',
                    'stack'           => 'stack0',
                ],
            ],
            'options' => [
                'scales' => [
                    'x' => [
                        'stacked' => true,
                    ],
                    'y' => [
                        'stacked' => true,
                        'beginAtZero' => true,
                    ],
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