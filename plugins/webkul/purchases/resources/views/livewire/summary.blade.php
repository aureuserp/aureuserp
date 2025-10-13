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
            font-size: 16px;
            color: #555;
        }

        :is(.dark .invoice-item) {
            color: #d1d5db;
        }

        .invoice-item span {
            font-weight: 600;
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

    @if (count($products))
        <div class="flex justify-end">
            <div class="invoice-container">
                @php
                    $subTotal = 0;
                    $totalTax = 0;
                    $grandTotal = 0;

                    foreach ($products as $product) {
                        $subTotal += $product['price_subtotal'];

                        $totalTax += $product['price_tax'];

                        $grandTotal += $product['price_total'];
                    }
                @endphp

                <div class="invoice-item">
                    <span>Untaxed Amount</span>
                    <span>{{ money($subTotal, $currency?->name) }}</span>
                </div>

                @if ($totalTax > 0)
                    <div class="invoice-item">
                        <span>Tax</span>
                        <span>{{ money($totalTax, $currency?->name) }}</span>
                    </div>
                @endif

                <div class="divider"></div>

                <div class="font-bold invoice-item">
                    <span>Total</span>
                    <span>{{ money($grandTotal, $currency?->name) }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
