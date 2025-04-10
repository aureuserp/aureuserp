<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333333;
            line-height: 1.6;
            margin: 0;
        }

        .agreement {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .agreement:last-child {
            page-break-after: auto;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .company-info {
            width: 50%;
            float: left;
        }

        .vendor-info {
            width: 45%;
            float: right;
            text-align: right;
            border-left: 2px solid #f0f0f0;
            padding-left: 20px;
        }

        .clearfix {
            clear: both;
        }

        .agreement-title {
            font-size: 24px;
            color: #1a4587;
            margin: 25px 0;
            padding: 15px 0;
            border-bottom: 2px solid #1a4587;
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 10px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table th {
            background: #1a4587;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .items-table tr:nth-child(odd) {
            background: #ffffff;
        }

        /* Dark Mode Support */
        :is(.dark .items-table th) {
            background: #0d2b57;
            color: #ffffff;
        }

        :is(.dark .items-table td) {
            border-bottom: 1px solid #444;
            color: #ddd;
        }

        :is(.dark .items-table tr:nth-child(even)) {
            background: #2d2d2d;
        }

        :is(.dark .items-table tr:nth-child(odd)) {
            background: #1e1e1e;
        }

        .summary {
            width: 100%;
            display: inline-block;
        }
        .summary table {
            float: right;
            width: 250px;
            padding-top: 5px;
            padding-bottom: 5px;
            white-space: nowrap;
        }
        .summary table.rtl {
            width: 280px;
        }
        .summary table.rtl {
            margin-right: 480px;
        }
        .summary table td {
            padding: 5px 10px;
        }
        .summary table td:nth-child(2) {
            text-align: center;
        }
        .summary table td:nth-child(3) {
            text-align: right;
        }

        .payment-info {
            clear: both;
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            color: #1a4587;
        }

        .payment-info-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="agreement">
        <!-- Header Section -->
        <div class="header">
            <!-- Company Address -->
            <div class="company-info">
                <div style="font-size: 28px; color: #1a4587; margin-bottom: 10px;">{{ $record->company->name }}</div>

                @if ($record->company->address)
                    <div>
                        {{ $record->company->address->street1 }}

                        @if ($record->company->address->street2)
                            ,{{ $record->company->address->street2 }}
                        @endif
                    </div>

                    <div>
                        {{ $record->company->address->city }},

                        @if ($record->company->address->state)
                            {{ $record->company->address->state->name }},
                        @endif

                        {{ $record->company->address->zip }}
                    </div>

                    @if ($record->company->address->country)
                        <div>
                            {{ $record->company->address->country->name }}
                        </div>
                    @endif

                    @if ($record->company->email)
                        <div>
                            Email:
                            {{ $record->company->email }}
                        </div>
                    @endif

                    @if ($record->company->phone)
                        <div>
                            Phone:
                            {{ $record->company->phone }}
                        </div>
                    @endif
                @endif
            </div>

            <!-- Customer Address -->
            <div class="vendor-info">
                <div>{{ $record->partner->name }}</div>

                @if ($record->partner->addresses->count())
                    <div>
                        @php
                            $partnerAddress = $record->partner->addresses->first();
                        @endphp

                        {{ $partnerAddress->street1 }}

                        @if ($partnerAddress->street2)
                            ,{{ $partnerAddress->street2 }}
                        @endif
                    </div>

                    <div>
                        {{ $partnerAddress->city }},

                        @if ($partnerAddress->state)
                            {{ $partnerAddress->state->name }},
                        @endif

                        {{ $partnerAddress->zip }}
                    </div>

                    @if ($partnerAddress->country)
                        <div>
                            {{ $partnerAddress->country->name }}
                        </div>
                    @endif

                    @if ($partnerAddress->email)
                        <div>
                            Email:
                            {{ $partnerAddress->email }}
                        </div>
                    @endif

                    @if ($partnerAddress->phone)
                        <div>
                            Phone:
                            {{ $partnerAddress->phone }}
                        </div>
                    @endif
                @endif
            </div>

            <div class="clearfix"></div>
        </div>

        <!-- Agreement Title -->
        <div class="agreement-title">
            @php
                $title = $record->state == \Webkul\Sale\Enums\OrderState::SALE ? 'Order' : 'Quotation';
            @endphp

            @if ($record->state == \Webkul\Sale\Enums\OrderState::SALE)
                {{ $title }} ID #{{ $record->name }}
            @else
                {{ $title }} ID #{{ $record->name }}
            @endif
        </div>

        <!-- Details Table -->
        <table class="details-table">
            <tr>
                @if ($record->date_order)
                    <td width="33%">
                        <strong>{{ $title }} Date</strong><br>
                        {{ $record->date_order }}
                    </td>
                @endif

                @if ($record->validity_date)
                    <td width="33%">
                        <strong>Expiration Date</strong><br>
                        {{ $record->validity_date }}
                    </td>
                @endif
            </tr>
        </table>

        <!-- Items Table -->
        @if (! $record->lines->isEmpty())
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>

                        @if (app(\Webkul\Sale\Settings\ProductSettings::class)->enable_uom)
                            <th>Unit</th>
                        @endif

                        <th>Unit Price</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($record->lines as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->product_uom_qty) }}</td>

                        @if (app(\Webkul\Sale\Settings\ProductSettings::class)->enable_uom)
                            <td>{{ $item->product->uom->name }}</td>
                        @endif

                        <td>{{ number_format($item->price_unit, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="summary">
            <table class="ltr">
                <tbody>
                    <tr>
                        <td>Subtotal</td>
                        <td>-</td>
                        <td>{{ $record->currency->symbol }} {{ number_format($record->amount_untaxed, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td>-</td>
                        <td>{{ $record->currency->symbol }} {{ number_format($record->amount_tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>-</td>
                        <td>-{{ $record->currency->symbol }} {{ number_format($record->total_discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #FFFFFF;">
                            <b>Grand Total</b>
                        </td>
                        <td style="border-top: 1px solid #FFFFFF;">-</td>
                        <td style="border-top: 1px solid #FFFFFF;">
                            <b>{{ $record->currency->symbol }} {{ number_format($record->amount_total, 2) }}</b>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Information Section -->
        @if ($record->name)
            <div class="payment-info">
                <div class="payment-info-title">Payment Information</div>
                <div>
                    Payment Communication: {{ $record->name }}
                    @if ($record?->partnerBank?->bank?->name || $record?->partnerBank?->account_number)
                        <br>
                        <span class="payment-info-details">on this account details:</span>
                        {{ $record?->partnerBank?->bank?->name ?? 'N/A' }}
                        ({{ $record?->partnerBank?->account_number ?? 'N/A' }})
                    @endif
                </div>
            </div>
        @endif
    </div>
</body>

</html>
