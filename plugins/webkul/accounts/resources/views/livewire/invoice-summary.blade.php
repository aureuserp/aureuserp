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
                    $totalDiscount = 0;
                    $totalTax = 0;
                    $grandTotal = 0;

                    foreach ($products as $product) {
                        $subTotal += floatval($product['price_subtotal']);

                        $totalTax += $product['price_tax'];

                        $totalDiscount += floatval($product['discount']);

                        $grandTotal += floatval($product['price_total']);
                    }
                @endphp

                <div class="invoice-item">
                    <span>Subtotal</span>
                    <span>{{ number_format($subTotal, 2) }}</span>
                </div>

                @if ($totalDiscount > 0)
                    <div class="invoice-item">
                        <span>Discount</span>
                        <span>-{{ number_format($totalDiscount, 2) }}</span>
                    </div>
                @endif

                @if ($totalTax > 0)
                    <div class="invoice-item">
                        <span>Tax Amount</span>
                        <span>{{ number_format($totalTax, 2) }}</span>  
                    </div>
                @endif

                <div class="divider"></div>

                <div class="font-bold invoice-item">
                    <span>Grand Total</span>
                    <span>{{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
