@php($prefix = 'point-of-sale::filament/pos/reports/sales-details.')

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
            font-size: 12px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
        }

        .header {
            background-color: #e6e9ec;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .title {
            font-size: 20px;
            text-align: center;
            margin: 0 0 6px 0;
        }

        .subtitle {
            text-align: center;
            margin: 0 0 12px 0;
        }

        .company {
            font-size: 11px;
            color: #6b7280;
        }

        .company strong {
            color: #333333;
        }

        .as-of {
            border: 1px solid #9ca3af;
            padding: 4px 10px;
            float: right;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .clearfix {
            clear: both;
        }

        .section {
            background-color: #e6e9ec;
            font-weight: bold;
            padding: 4px 8px;
            margin-top: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .numeric {
            text-align: right;
            white-space: nowrap;
        }

        .group td {
            font-weight: bold;
        }

        .child td:first-child {
            padding-left: 28px;
            color: #1f4e79;
        }

        .total td {
            font-weight: bold;
            border-top: 1px solid #9ca3af;
        }

        .narrow {
            width: 60%;
            margin-left: auto;
        }

        .note {
            margin-top: 28px;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ __($prefix.'title', ['config' => $session->config?->name]) }}</h1>

        <p class="subtitle">{{ __($prefix.'session', ['session' => $session->name]) }}</p>

        <div class="company">
            <strong>{{ $session->company?->name }}</strong><br>
            {{ $session->company?->street1 }}<br>
            {{ $session->company?->city }} {{ $session->company?->zip }}<br>
            {{ $session->company?->country?->name }}
        </div>
    </div>

    <div class="as-of">{{ __($prefix.'as-of', ['date' => now()->translatedFormat('d/m/Y')]) }}</div>

    <div class="clearfix"></div>

    @foreach ([['sales', $sales, $sales_total, $sales_taxes], ['refunds', $refunds, $refunds_total, $refunds_taxes]] as [$key, $groups, $groupTotal, $groupTaxes])
        @continue (! $groups)

        <div class="section">{{ __($prefix.$key) }}</div>

        <table>
            @foreach ($groups as $group)
                <tr class="group">
                    <td>{{ $group['name'] }}</td>
                    <td class="numeric">{{ number_format($group['quantity'], 1) }}</td>
                    <td class="numeric">{{ $money($group['amount']) }}</td>
                </tr>

                @foreach ($group['products'] as $product)
                    <tr class="child">
                        <td>{{ $product['name'] }}</td>
                        <td class="numeric">{{ number_format($product['quantity'], 1) }}</td>
                        <td class="numeric">
                            {{ $money($product['amount']) }}

                            @if (! float_is_zero($product['discount'], precisionDigits: 2))
                                {{ __($prefix.'discount', ['percent' => number_format($product['discount'], 1)]) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <tr class="total">
                <td>{{ __($prefix.'total') }}</td>
                <td class="numeric">{{ number_format($groupTotal['quantity'], 1) }}</td>
                <td class="numeric">{{ $money($groupTotal['amount']) }}</td>
            </tr>
        </table>

        @if ($groupTaxes)
            <div class="narrow">
                <div class="section">{{ __($prefix.'taxes-on-'.$key) }}</div>

                <table>
                    @foreach ($groupTaxes as $tax)
                        <tr>
                            <td><strong>{{ $tax['name'] }}</strong></td>
                            <td class="numeric">{{ $money($tax['amount']) }}</td>
                            <td class="numeric">{{ $money($tax['base']) }}</td>
                        </tr>
                    @endforeach

                    <tr class="total">
                        <td>{{ __($prefix.'total') }}</td>
                        <td class="numeric">{{ $money(collect($groupTaxes)->sum('amount')) }}</td>
                        <td class="numeric">{{ $money(collect($groupTaxes)->sum('base')) }}</td>
                    </tr>
                </table>
            </div>
        @endif
    @endforeach

    @if ($payments)
        <div class="narrow">
            <div class="section">{{ __($prefix.'payments') }}</div>

            <table>
                @foreach ($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment['name'] }}</strong></td>
                        <td class="numeric">{{ $money($payment['amount']) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if ($discounts['quantity'])
        <div class="section">{{ __($prefix.'discounts') }}</div>

        <p>
            <strong>{{ __($prefix.'number-of-discounts') }}</strong>: {{ $discounts['quantity'] }}<br>
            <strong>{{ __($prefix.'amount-of-discounts') }}</strong>: {{ $money($discounts['amount']) }}
        </p>
    @endif

    @if ($invoices)
        <div class="section">{{ __($prefix.'invoices') }}</div>

        <table>
            @foreach ($invoices as $invoice)
                <tr>
                    <td><strong>{{ $invoice['name'] }}</strong></td>
                    <td>{{ __($prefix.'order', ['order' => $invoice['order']]) }}</td>
                    <td class="numeric">{{ $money($invoice['amount']) }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="2">{{ __($prefix.'total') }}</td>
                <td class="numeric">{{ $money(collect($invoices)->sum('amount')) }}</td>
            </tr>
        </table>
    @endif

    <div class="section">{{ __($prefix.'session-control') }}</div>

    <p>
        <strong>{{ __($prefix.'total') }}</strong>: {{ $money(collect($payments)->sum('amount')) }}<br>
        <strong>{{ __($prefix.'number-of-transactions') }}</strong>: {{ $orders->count() }}
    </p>

    @if ($control['default_cash_details'])
        <table>
            <tr>
                <td>{{ __($prefix.'name') }}</td>
                <td class="numeric">{{ __($prefix.'expected') }}</td>
            </tr>

            <tr>
                <td><strong>{{ $control['default_cash_details']['name'] }} {{ $session->name }}</strong></td>
                <td class="numeric">{{ $money($control['default_cash_details']['amount']) }}</td>
            </tr>
        </table>
    @endif

    @if ($session->opening_notes)
        <div class="note">
            <strong>{{ __($prefix.'opening-note') }}</strong> {{ $session->opening_notes }}
        </div>
    @endif

    @if ($session->closing_notes)
        <div class="note">
            <strong>{{ __($prefix.'closing-note') }}</strong> {{ $session->closing_notes }}
        </div>
    @endif
</body>
</html>
