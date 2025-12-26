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
                        ->where('invoice_date_due', '<', now())
                        ->whereNot('payment_state', PaymentState::PAID)
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('invoice_date_due', '<', now())
                        ->whereNot('payment_state', PaymentState::PAID)
                        ->sum('amount_total'),
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
                        ->sum('amount_total'),
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
                        ->where('invoice_date_due', '<', now())
                        ->whereNot('payment_state', PaymentState::PAID)
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->where('invoice_date_due', '<', now())
                        ->whereNot('payment_state', PaymentState::PAID)
                        ->sum('amount_total'),
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
                        ->sum('amount_total'),
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

        // Checks (sequence holes, unhashed entries, etc.)
        $data['checks'] = [
            'has_sequence_holes' => $this->hasSequenceHoles(),
            'has_unhashed_entries' => $this->hasUnhashedEntries(),
        ];

        // Actions (create, reconcile, etc.)
        $data['actions'] = $this->getActions();

        // Graph
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

    private function hasSequenceHoles(): bool
    {
        // TODO: Implement logic to check for sequence holes in journal entries
        return false;
    }

    private function hasUnhashedEntries(): bool
    {
        // TODO: Implement logic to check for unhashed entries in journal
        return false;
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
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month'      => $date->format('Y-m'),
                'label'      => $date->format('M Y'),
                'start_date' => $date->startOfMonth()->toDateString(),
                'end_date'   => $date->endOfMonth()->toDateString(),
            ]);
        }
        $data = $months->map(function ($month) {
            $total = Move::where('journal_id', $this->journal->id)
                ->where('state', 'posted')
                ->whereBetween('invoice_date', [$month['start_date'], $month['end_date']])
                ->sum('amount_total');
            return [
                'label' => $month['label'],
                'value' => (float) $total,
            ];
        });
        $color = $this->journalColor ?? '#3b82f6';
        if (! str_starts_with($color, '#')) {
            $color = '#3b82f6';
        }
        $type = $this->journal->type;
        $graphType = in_array($type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]) ? 'line' : 'bar';
        return [
            'type'     => $graphType,
            'labels'   => $data->pluck('label')->toArray(),
            'datasets' => [
                [
                    'label'           => $this->journal->name,
                    'data'            => $data->pluck('value')->toArray(),
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
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
