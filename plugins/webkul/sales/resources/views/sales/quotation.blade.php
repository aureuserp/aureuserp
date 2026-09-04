<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        @if ($isPdf ?? false)
            body {
                margin: 0;
            }
        @endif

        .quotation-document,
        .quotation-document table,
        .quotation-document th,
        .quotation-document td,
        .quotation-document div,
        .quotation-document span,
        .quotation-document p,
        .quotation-document b,
        .quotation-document strong {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        .quotation-document {
            font-size: 14px;
            color: #333333;
            line-height: 1.6;
        }

        :is(.dark .quotation-document) {
            color: #d4d4d8;
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

        .company-name {
            font-size: 28px;
            color: #1a4587;
            margin-bottom: 10px;
        }

        :is(.dark .company-name) {
            color: #93b4e6;
        }

        .vendor-info {
            width: 45%;
            float: right;
            text-align: right;
            border-left: 2px solid #f0f0f0;
            padding-left: 20px;
        }

        :is(.dark .vendor-info) {
            border-left-color: #3f3f46;
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

        :is(.dark .agreement-title) {
            color: #93b4e6;
            border-bottom-color: #3b5f92;
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
            color: #ffffff;
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

        .items-table tr:nth-child(odd) {
            background: #ffffff;
        }

        .items-table .numeric,
        .summary .amount {
            text-align: {{ $isRtl ? 'left' : 'right' }};
        }

        :is(.dark .items-table th) {
            background: #0d2b57;
            color: #ffffff;
        }

        :is(.dark .items-table td) {
            border-bottom-color: #3f3f46;
            color: #d4d4d8;
        }

        :is(.dark .items-table tr:nth-child(even)) {
            background: #232326;
        }

        :is(.dark .items-table tr:nth-child(odd)) {
            background: #1a1a1d;
        }

        .options-title {
            clear: both;
            font-size: 18px;
            color: #1a4587;
            margin-top: 30px;
            padding-bottom: 8px;
            border-bottom: 1px solid #1a4587;
        }

        :is(.dark .options-title) {
            color: #93b4e6;
            border-bottom-color: #3b5f92;
        }

        .summary {
            width: 100%;
            display: inline-block;
        }

        .summary table {
            float: right;
            width: 280px;
            padding-top: 5px;
            padding-bottom: 5px;
            white-space: nowrap;
        }

        .summary table td {
            padding: 5px 10px;
        }

        .summary .grand-total td {
            border-top: 1px solid #e9ecef;
        }

        :is(.dark .summary) {
            color: #d4d4d8;
        }

        :is(.dark .summary .grand-total td) {
            border-top-color: #3f3f46;
        }

        .terms {
            clear: both;
            margin-top: 30px;
        }

        .terms-body {
            margin-top: 10px;
        }

        .payment-info {
            clear: both;
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            color: #1a4587;
        }

        :is(.dark .payment-info) {
            color: #93b4e6;
        }

        .payment-info-title {
            font-weight: {{ $isRtl ? 'bold' : '600' }};
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    @php
        $enableUom = settings(\Webkul\Product\Settings\ProductSettings::class)->enable_uom;

        $showDiscount = settings(\Webkul\Sale\Settings\PriceSettings::class)->enable_discount
            && ($record->lines->contains(fn ($line) => (float) $line->discount > 0)
                || $record->optionalLines->contains(fn ($option) => (float) $option->discount > 0));

        $terms = filled(trim(strip_tags((string) $record->note, '<img>'))) ? $record->note : null;
    @endphp

    <div class="quotation-document">
        <div class="agreement">
            <!-- Header Section -->
            <div class="header">
                <!-- Company Address -->
                <div class="company-info">
                    <div class="company-name">{{ $record->company->name }}</div>

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

                {{ __('sales::app.documents.title', ['document' => $title, 'name' => $record->name]) }}
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    @if ($record->date_order)
                        <td width="33%">
                            <strong>{{ __('sales::app.documents.date', ['document' => $title]) }}</strong><br>
                            {{ $record->date_order }}
                        </td>
                    @endif

                    @if ($record->validity_date)
                        <td width="33%">
                            <strong>{{ __('sales::app.documents.expiration-date') }}</strong><br>
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
                            <th>{{ __('sales::app.documents.product') }}</th>
                            <th>{{ __('sales::app.documents.quantity') }}</th>

                            @if ($enableUom)
                                <th>{{ __('sales::app.documents.unit') }}</th>
                            @endif

                            <th>{{ __('sales::app.documents.unit-price') }}</th>

                            @if ($showDiscount)
                                <th>{{ __('sales::app.documents.discount-percentage') }}</th>
                            @endif

                            <th>{{ __('sales::app.documents.amount') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($record->lines as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->product_uom_qty) }}</td>

                                @if ($enableUom)
                                    <td>{{ $item->product->uom->name }}</td>
                                @endif

                                <td>{{ $record->currency->symbol }} {{ number_format($item->price_unit, 2) }}</td>

                                @if ($showDiscount)
                                    <td>{{ floatval($item->discount) }}%</td>
                                @endif

                                <td class="numeric">{{ $record->currency->symbol }} {{ number_format($item->price_subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Optional Products Table -->
            @if (in_array($record->state, [\Webkul\Sale\Enums\OrderState::DRAFT, \Webkul\Sale\Enums\OrderState::SENT]) && ! $record->optionalLines->isEmpty())
                <div class="options-title">{{ __('sales::app.documents.options') }}</div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>{{ __('sales::app.documents.product') }}</th>
                            <th>{{ __('sales::app.documents.quantity') }}</th>

                            @if ($enableUom)
                                <th>{{ __('sales::app.documents.unit') }}</th>
                            @endif

                            <th>{{ __('sales::app.documents.unit-price') }}</th>

                            @if ($showDiscount)
                                <th>{{ __('sales::app.documents.discount-percentage') }}</th>
                            @endif

                            <th>{{ __('sales::app.documents.amount') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($record->optionalLines as $option)
                            @php
                                $optionAmount = $option->price_unit * $option->quantity * (1 - (($option->discount ?? 0) / 100));
                            @endphp

                            <tr>
                                <td>{{ filled($option->name) ? $option->name : $option->product->name }}</td>
                                <td>{{ number_format($option->quantity) }}</td>

                                @if ($enableUom)
                                    <td>{{ $option->uom?->name ?? $option->product->uom->name }}</td>
                                @endif

                                <td>{{ $record->currency->symbol }} {{ number_format($option->price_unit, 2) }}</td>

                                @if ($showDiscount)
                                    <td>{{ floatval($option->discount) }}%</td>
                                @endif

                                <td class="numeric">{{ $record->currency->symbol }} {{ number_format($optionAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="summary">
                <table>
                    <tbody>
                        <tr>
                            <td>{{ __('sales::app.documents.subtotal') }}</td>
                            <td>-</td>
                            <td class="amount">{{ $record->currency->symbol }} {{ number_format($record->amount_untaxed, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('sales::app.documents.tax') }}</td>
                            <td>-</td>
                            <td class="amount">{{ $record->currency->symbol }} {{ number_format($record->amount_tax, 2) }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td><b>{{ __('sales::app.documents.grand-total') }}</b></td>
                            <td>-</td>
                            <td class="amount"><b>{{ $record->currency->symbol }} {{ number_format($record->amount_total, 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Terms and Conditions Section -->
            @if ($terms)
                <div class="terms">
                    <div class="options-title">{{ __('sales::app.documents.terms-and-conditions') }}</div>

                    <div class="terms-body">{!! $terms !!}</div>
                </div>
            @endif

            <!-- Payment Information Section -->
            @if ($record->name)
                <div class="payment-info">
                    <div class="payment-info-title">{{ __('sales::app.documents.payment-information') }}</div>
                    <div>
                        {{ __('sales::app.documents.payment-communication') }}: {{ $record->name }}
                        @if ($record?->partnerBank?->bank?->name || $record?->partnerBank?->account_number)
                            <br>
                            <span class="payment-info-details">{{ __('sales::app.documents.account-details') }}</span>
                            {{ $record?->partnerBank?->bank?->name ?? 'N/A' }}
                            ({{ $record?->partnerBank?->account_number ?? 'N/A' }})
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
