<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('accounting::filament/clusters/reporting.pages.profit-loss.navigation.title') }}</title>
    <style>
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

        @page {
            margin: 1.5cm 1.5cm;
            size: A4 portrait;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            color: #111827;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 10pt;
            color: #6b7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .section-header {
            background-color: #f9fafb;
            font-weight: bold;
            font-size: 10pt;
            padding: 8px 10px;
            border-bottom: 2px solid #d1d5db;
            color: #111827;
        }
        
        .account-line {
            padding: 5px 10px 5px 40px;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .total-line {
            padding: 8px 10px;
            font-weight: bold;
            border-bottom: 1px solid #9ca3af;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        
        .net-income {
            padding: 10px;
            font-weight: bold;
            font-size: 10pt;
            border-top: 2px solid #111827;
            border-bottom: 2px solid #111827;
            margin-top: 10px;
            background-color: #f9fafb;
            color: #111827;
        }
        
        .amount {
            text-align: right;
            white-space: nowrap;
        }
        
        .empty-message {
            padding: 5px 10px 5px 30px;
            font-style: italic;
            color: #9ca3af;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('accounting::filament/clusters/reporting.pages.profit-loss.navigation.title') }}</h1>
        <p>{{ __('accounting::filament/clusters/reporting/pages/profit-loss.period', ['from' => format_date($data['date_from']), 'to' => format_date($data['date_to'])]) }}</p>
    </div>

    <table>
        @foreach($data['sections'] as $section)
            <tr>
                <td colspan="2" class="section-header">{{ $section['title'] }}</td>
            </tr>
            
            @if(!empty($section['accounts']))
                @foreach($section['accounts'] as $account)
                    <tr>
                        <td class="account-line">
                            {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                        </td>
                        <td class="account-line amount">{{ money($account['balance'], $currency) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="total-line">{{ $section['total_label'] }}</td>
                    <td class="total-line amount">{{ money($section['total'], $currency) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="2" class="empty-message">{{ $section['empty_message'] }}</td>
                </tr>
            @endif

            <tr><td colspan="2" style="padding: 5px 0;"></td></tr>
        @endforeach

        <tr>
            <td class="net-income">{{ $data['is_profit'] ? __('accounting::filament/clusters/reporting/pages/profit-loss.totals.net-profit') : __('accounting::filament/clusters/reporting/pages/profit-loss.totals.net-loss') }}</td>
            <td class="net-income amount">{{ money(abs($data['net_income']), $currency) }}</td>
        </tr>
    </table>

    <div class="footer">
        <div>{{ __('accounting::filament/clusters/reporting/pages/profit-loss.footer.generated-on', ['date' => format_date(now(), 'LLL')]) }}</div>
    </div>
</body>
</html>
