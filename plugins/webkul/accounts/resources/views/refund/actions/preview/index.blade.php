<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        html,
        body,
        table,
        th,
        td,
        div,
        span,
        p,
        b,
        strong {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        body {
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
            float: {{ $isRtl ? 'right' : 'left' }};
            text-align: {{ $isRtl ? 'right' : 'left' }};
        }

        .vendor-info {
            width: 45%;
            float: {{ $isRtl ? 'left' : 'right' }};
            text-align: {{ $isRtl ? 'left' : 'right' }};
            border-{{ $isRtl ? 'right' : 'left' }}: 2px solid #f0f0f0;
            padding-{{ $isRtl ? 'right' : 'left' }}: 20px;
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
            text-align: {{ $isRtl ? 'right' : 'left' }};
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 10px;
            vertical-align: top;
            text-align: {{ $isRtl ? 'right' : 'left' }};
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
            text-align: {{ $isRtl ? 'right' : 'left' }};
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            text-align: {{ $isRtl ? 'right' : 'left' }};
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .summary {
            width: 100%;
            display: inline-block;
        }
        .summary table {
            float: {{ $isRtl ? 'right' : 'left' }};
            width: 250px;
            padding-top: 5px;
            padding-bottom: 5px;
            white-space: nowrap;
        }
        .summary table td {
            padding: 5px 10px;
        }
        .summary table td:nth-child(2) {
            text-align: center;
        }
        .summary table td.amount {
            text-align: {{ $isRtl ? 'left' : 'right' }};
        }

        .payment-info {
            clear: both;
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            text-align: {{ $isRtl ? 'right' : 'left' }};
        }

        .payment-info-title {
            font-weight: {{ $isRtl ? 'bold' : '600' }};
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

                @if ($record->company->partner)
                    <div>
                        {{ $record->company->partner->street1 }}

                        @if ($record->company->partner->street2)
                            ,{{ $record->company->partner->street2 }}
                        @endif
                    </div>

                    <div>
                        {{ $record->company->partner->city }},

                        @if ($record->company->partner->state)
                            {{ $record->company->partner->state->name }},
                        @endif

                        {{ $record->company->partner->zip }}
                    </div>

                    @if ($record->company->partner->country)
                        <div>
                            {{ $record->company->partner->country->name }}
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

                <div>
                    {{ $record->partner->street1 }}

                    @if ($record->partner->street2)
                        ,{{ $record->partner->street2 }}
                    @endif
                </div>

                <div>
                    {{ $record->partner->city }},

                    @if ($record->partner->state)
                        {{ $record->partner->state->name }},
                    @endif

                    {{ $record->partner->zip }}
                </div>

                @if ($record->partner->country)
                    <div>
                        {{ $record->partner->country->name }}
                    </div>
                @endif

                @if ($record->partner->email)
                    <div>
                        Email:
                        {{ $record->partner->email }}
                    </div>
                @endif

                @if ($record->partner->phone)
                    <div>
                        Phone:
                        {{ $record->partner->phone }}
                    </div>
                @endif
            </div>

            <div class="clearfix"></div>
        </div>

        <!-- Agreement Title -->
        <div class="agreement-title">
            {{ __('accounts::account-manager.documents.titles.refund', ['name' => $record->name]) }}
        </div>

        <!-- Details Table -->
        <table class="details-table">
            <tr>
                @if ($record->invoice_date)
                    <td width="33%">
                        <strong>{{ __('accounts::account-manager.documents.labels.refund-date') }}</strong><br>
                        {{ $record->invoice_date }}
                    </td>
                @endif

                @if ($record->ref)
                    <td width="33%">
                        <strong>{{ __('accounts::account-manager.documents.labels.source') }}</strong><br>
                        {{ $record->ref }}
                    </td>
                @endif

                @if ($record->invoice_date_due)
                    <td width="33%">
                        <strong>{{ __('accounts::account-manager.documents.labels.due-date') }}</strong><br>
                        {{ $record->invoice_date_due?->format('Y-m-d') }}
                    </td>
                @endif
            </tr>
        </table>

        <!-- Items Table -->
        @if (! $record->invoiceLines->isEmpty())
            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('accounts::account-manager.documents.labels.product') }}</th>
                        <th>{{ __('accounts::account-manager.documents.labels.quantity') }}</th>

                        @if (settings(\Webkul\Product\Settings\ProductSettings::class)->enable_uom)
                            <th>{{ __('accounts::account-manager.documents.labels.unit') }}</th>
                        @endif

                        <th>{{ __('accounts::account-manager.documents.labels.unit-price') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($record->invoiceLines as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->quantity) }}</td>

                        @if (settings(\Webkul\Product\Settings\ProductSettings::class)->enable_uom)
                            <td>{{ $item->product->uom->name }}</td>
                        @endif

                        <td class="amount">{{ money($item->price_unit, $record->currency->name) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="summary">
            <table>
                <tbody>
                    <tr>
                        <td>{{ __('accounts::account-manager.documents.labels.subtotal') }}</td>
                        <td>-</td>
                        <td class="amount">{{ money($record->amount_untaxed, $record->currency->name) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('accounts::account-manager.documents.labels.tax') }}</td>
                        <td>-</td>
                        <td class="amount">{{ money($record->amount_tax, $record->currency->name) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('accounts::account-manager.documents.labels.discount') }}</td>
                        <td>-</td>
                        <td class="amount">-{{ money($record->total_discount, $record->currency->name) }}</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #FFFFFF;">
                            <b>{{ __('accounts::account-manager.documents.labels.grand-total') }}</b>
                        </td>
                        <td style="border-top: 1px solid #FFFFFF;">-</td>
                        <td class="amount" style="border-top: 1px solid #FFFFFF;">
                            <b>{{ money($record->amount_total, $record->currency->name) }}</b>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Information Section -->
        @if ($record->name)
            <div class="payment-info">
                <div class="payment-info-title">{{ __('accounts::account-manager.documents.labels.payment-information') }}</div>
                <div>
                    {{ __('accounts::account-manager.documents.labels.payment-communication') }}: {{ $record->name }}
                    @if ($record?->partnerBank?->bank?->name || $record?->partnerBank?->account_number)
                        <br>
                        <span>{{ __('accounts::account-manager.documents.labels.account-details') }}</span>
                        {{ $record?->partnerBank?->bank?->name ?? 'N/A' }}
                        ({{ $record?->partnerBank?->account_number ?? 'N/A' }})
                    @endif
                </div>
            </div>
        @endif
    </div>
</body>
</html>
