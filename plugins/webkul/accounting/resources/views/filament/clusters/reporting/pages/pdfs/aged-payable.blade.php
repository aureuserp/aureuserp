<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('accounting::filament/clusters/reporting.pages.aged-payable.navigation.title') }} - {{ format_date($asOfDate) }}</title>
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
            margin: 1cm 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.3;
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
        
        table th {
            background-color: #f3f4f6;
            padding: 6px 4px;
            text-align: right;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 2px solid #d1d5db;
        }
        
        table th:first-child {
            text-align: left;
        }
        
        table td {
            padding: 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
            text-align: right;
        }
        
        table td:first-child {
            text-align: left;
            font-weight: {{ $isRtl ? 'normal' : '500' }};
            color: #111827;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #e5e7eb;
            border-top: 2px solid #6b7280;
            color: #111827;
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
        
        .page-number:after {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('accounting::filament/clusters/reporting.pages.aged-payable.navigation.title') }}</h1>
        <p>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.as-of', ['date' => format_date($asOfDate, 'LL')]) }}</p>
    </div>

    @if(empty($partners))
        <div class="no-data">{{ __('accounting::filament/clusters/reporting.common.no-data') }}</div>
    @else
        @php
            $totalAtDate = 0;
            $totalPeriod1 = 0;
            $totalPeriod2 = 0;
            $totalPeriod3 = 0;
            $totalPeriod4 = 0;
            $totalOlder = 0;
            $grandTotal = 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>{{ __('accounting::filament/clusters/reporting.common.partner') }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting.common.not-due') }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.table.columns.period-range', ['from' => 1, 'to' => $period]) }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.table.columns.period-range', ['from' => $period + 1, 'to' => $period * 2]) }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.table.columns.period-range', ['from' => ($period * 2) + 1, 'to' => $period * 3]) }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.table.columns.period-range', ['from' => ($period * 3) + 1, 'to' => $period * 4]) }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.table.columns.older-than', ['days' => $period * 4]) }}</th>
                    <th>{{ __('accounting::filament/clusters/reporting.common.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($partners as $partner)
                    @php
                        $totalAtDate += $partner['at_date'];
                        $totalPeriod1 += $partner['period_1'];
                        $totalPeriod2 += $partner['period_2'];
                        $totalPeriod3 += $partner['period_3'];
                        $totalPeriod4 += $partner['period_4'];
                        $totalOlder += $partner['older'];
                        $grandTotal += $partner['total'];
                    @endphp
                    <tr>
                        <td>{{ $partner['partner_name'] }}</td>
                        <td>{{ money($partner['at_date'], $currency) }}</td>
                        <td>{{ money($partner['period_1'], $currency) }}</td>
                        <td>{{ money($partner['period_2'], $currency) }}</td>
                        <td>{{ money($partner['period_3'], $currency) }}</td>
                        <td>{{ money($partner['period_4'], $currency) }}</td>
                        <td>{{ money($partner['older'], $currency) }}</td>
                        <td>{{ money($partner['total'], $currency) }}</td>
                    </tr>
                    
                    @if(in_array($partner['id'], $expandedPartners ?? []) && isset($partnerLines[$partner['id']]))
                        @foreach($partnerLines[$partner['id']] as $line)
                            <tr class="line-row">
                                <td style="padding-left: 20px; font-weight: 400;">{{ $line['move_name'] }}</td>
                                <td>{{ $line['at_date'] != 0 ? money($line['at_date'], $currency) : '' }}</td>
                                <td>{{ $line['period_1'] != 0 ? money($line['period_1'], $currency) : '' }}</td>
                                <td>{{ $line['period_2'] != 0 ? money($line['period_2'], $currency) : '' }}</td>
                                <td>{{ $line['period_3'] != 0 ? money($line['period_3'], $currency) : '' }}</td>
                                <td>{{ $line['period_4'] != 0 ? money($line['period_4'], $currency) : '' }}</td>
                                <td>{{ $line['older'] != 0 ? money($line['older'], $currency) : '' }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                <tr class="total-row">
                    <td>{{ __('accounting::filament/clusters/reporting.common.total') }}</td>
                    <td>{{ money($totalAtDate, $currency) }}</td>
                    <td>{{ money($totalPeriod1, $currency) }}</td>
                    <td>{{ money($totalPeriod2, $currency) }}</td>
                    <td>{{ money($totalPeriod3, $currency) }}</td>
                    <td>{{ money($totalPeriod4, $currency) }}</td>
                    <td>{{ money($totalOlder, $currency) }}</td>
                    <td>{{ money($grandTotal, $currency) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div class="page-number"></div>
        <div>{{ __('accounting::filament/clusters/reporting/pages/aged-payable.footer.generated-on', ['date' => format_date(now(), 'LLL')]) }}</div>
    </div>
</body>
</html>
