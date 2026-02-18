<div>
    <style>
        .invoice-container {
            width: 350px;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
        }

        :is(.dark .invoice-container) {
            background-color: rgb(36 36 39);
            border: 1px solid rgb(44 44 47);
        }

        .invoice-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: #555;
            gap: 8px;
        }

        :is(.dark .invoice-item) {
            color: #d1d5db;
        }

        .invoice-item span {
            font-weight: 600;
        }

        .invoice-item.font-bold span {
            font-weight: 700 !important;
            font-size: 16px;
        }

        .invoice-item.font-semibold span {
            font-weight: 600 !important;
        }

        .invoice-item button {
            flex-shrink: 0;
        }

        .divider {
            border-bottom: 1px solid #ddd;
            margin: 12px 0;
        }

        :is(.dark .divider) {
            border-bottom-color: #374151;
        }

        :is(.dark .total) {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f3f4f6;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }

        :is(.dark .footer) {
            color: #9ca3af;
        }
    </style>

    <div class="flex justify-end">
        <div class="invoice-container">
            <div class="invoice-item">
                <span>Untaxed Amount</span>
                <span>{{ money($subtotal, $currency?->name) }}</span>
            </div>

            @if ($totalTax > 0)
                <div class="invoice-item">
                    <span>Tax</span>
                    <span>{{ money($totalTax, $currency?->name) }}</span>
                </div>
            @endif

            @if ($rounding != 0)
                <div class="invoice-item">
                    <span>Cash Rounding</span>
                    <span>{{ money($rounding, $currency?->name) }}</span>
                </div>
            @endif

            <div class="divider"></div>

            <div class="invoice-item font-semibold">
                <span>Total</span>
                <span>{{ money($grandTotal, $currency?->name) }}</span>
            </div>

            <!-- Reconciled Payments Section -->
            @if ($reconciledPayments && ! empty($reconciledPayments['lines']))
                <div class="divider"></div>
                
                @foreach ($reconciledPayments['lines'] ?? [] as $line)
                    <div class="invoice-item items-center">
                        <div class="flex items-center gap-2">
                            {{ ($this->unReconcileAction())(['partial_id' => $line['partial_id']]) }}

                            <div class="flex-1">
                                <x-filament::link :href="$this->getResourceUrl($line)">
                                    {{ $line['ref'] }}
                                </x-filament::link>
                                
                                @if (isset($line['date']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Paid on {{ $line['date']->format('M D, Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <span class="font-semibold">
                            {{ $line['amount_currency'] }}
                        </span>
                    </div>
                @endforeach
            @endif

            <!-- Reconcilable Payments Section -->
            @if ($reconcilablePayments && $reconcilablePayments['outstanding'])
                <div class="divider"></div>

                <div class="mt-4 font-semibold">
                    {{ $reconcilablePayments['title'] }}
                </div>

                @foreach ($reconcilablePayments['lines'] ?? [] as $line)
                    <div class="invoice-item items-center">
                        <div class="flex items-center gap-2">
                            {{ ($this->reconcileAction())(['lineId' => $line['id']]) }}

                            <div class="flex-1">
                                <x-filament::link :href="$this->getResourceUrl($line)">
                                    {{ $line['journal_name'] }}
                                </x-filament::link>
                                
                                @if (isset($line['date']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $line['date'] }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <span class="font-semibold">
                            {{ money($line['amount'], $currency?->name) }}
                        </span>
                    </div>
                @endforeach
            @endif

            <!-- Amount due or residual -->
            @if ($record?->state === \Webkul\Account\Enums\MoveState::POSTED)
                <div class="divider"></div>

                <div class="invoice-item total font-bold">
                    <span>
                        Amount Due
                    </span>

                    <span>
                        {{ money($record->amount_residual, $currency?->name) }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</div>
