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

    public function getStats()
    {
        $baseQuery = Move::where('journal_id', $this->journal->id);
            
        if (in_array($this->journal->type, [JournalType::SALE, JournalType::PURCHASE])) {
            return [
                'to_validate' => [
                    'label' => 'To Validate',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'draft']),
                    'value' => (clone $baseQuery)->where('state', MoveState::DRAFT)->count(),
                    'amount' => $amount = (clone $baseQuery)->where('state', MoveState::DRAFT)->sum('amount_total'),
                    'formatted_amount' => money($amount),
                ],
                'unpaid' => [
                    'label' => 'Unpaid',
                    'url'   => $this->getUrl('index', ['activeTableView' => 'to_pay']),
                    'value' => (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
                        ->whereNotIn('payment_state', [PaymentState::PAID, PaymentState::IN_PAYMENT])
                        ->count(),
                    'amount' => $amount = (clone $baseQuery)
                        ->where('state', MoveState::POSTED)
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
        } elseif ($this->journal->type == JournalType::GENERAL) {
            return [];
        } else {
            return [
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
    }

    public function getChartData()
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

        return [
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
        return view('accounting::filament.widgets.journal-chart-widget');
    }
}
