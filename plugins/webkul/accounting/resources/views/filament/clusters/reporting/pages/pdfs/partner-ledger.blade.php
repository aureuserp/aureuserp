<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('accounting::filament/clusters/reporting.common.from-to', ['report' => __('accounting::filament/clusters/reporting.pages.partner-ledger.navigation.title'), 'from' => format_date($data['date_from']), 'to' => format_date($data['date_to'])]) }}</title>
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
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 2px solid #d1d5db;
        }
        
        table th.text-right {
            text-align: right;
        }
        
        table td {
            padding: 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        
        .partner-header {
            background-color: #f9fafb;
            font-weight: bold;
            color: #111827;
        }
        
        .move-row {
            padding-left: 15px !important;
            color: #4b5563;
        }
        
        .opening-balance {
            background-color: #fef3c7;
            font-style: italic;
            padding-left: 15px !important;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #e5e7eb;
            border-top: 2px solid #6b7280;
            color: #111827;
        }
        
        .text-right {
            text-align: right;
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
            content: "{{ __('accounting::filament/clusters/reporting/pages/partner-ledger.pdf.page') }} " counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('accounting::filament/clusters/reporting.pages.partner-ledger.navigation.title') }}</h1>
        <p>{{ __('accounting::filament/clusters/reporting/pages/partner-ledger.pdf.period', ['from' => format_date($data['date_from'], 'LL'), 'to' => format_date($data['date_to'], 'LL')]) }}</p>
        <p>{{ __('accounting::filament/clusters/reporting/pages/partner-ledger.pdf.currency', ['currency' => $currency]) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('accounting::filament/clusters/reporting.common.partner') }}</th>
                <th>{{ __('accounting::filament/clusters/reporting.common.journal') }}</th>
                <th>{{ __('accounting::filament/clusters/reporting.common.account') }}</th>
                <th>{{ __('accounting::filament/clusters/reporting.common.invoice-date') }}</th>
                <th>{{ __('accounting::filament/clusters/reporting.common.due-date') }}</th>
                <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.debit') }}</th>
                <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.credit') }}</th>
                <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp

            @foreach($data['partners'] as $partner)
                @php
                    $totalDebit += $partner->period_debit;
                    $totalCredit += $partner->period_credit;
                    $isExpanded = in_array($partner->id, $expandedPartners ?? []);
                    $runningBalance = $partner->opening_balance;
                @endphp

                {{-- Partner Header --}}
                <tr class="partner-header">
                    <td>{{ $partner->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ format_number($partner->period_debit) }}</td>
                    <td class="text-right">{{ format_number($partner->period_credit) }}</td>
                    <td class="text-right">{{ format_number($partner->ending_balance) }}</td>
                </tr>

                {{-- Opening Balance --}}
                @if($partner->opening_balance != 0 && $isExpanded)
                    <tr class="opening-balance">
                        <td class="move-row">{{ __('accounting::filament/clusters/reporting.common.opening-balance') }}</td>
                        <td>{{ format_date($data['date_from']) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ format_number($partner->opening_balance) }}</td>
                    </tr>
                @endif

                {{-- Move Lines --}}
                @if($isExpanded)
                    @foreach($getPartnerMoves($partner->id) as $move)
                        @php
                            $runningBalance += ($move['debit'] - $move['credit']);
                        @endphp
                        <tr>
                            <td class="move-row">{{ $move['move_name'] }}@if($move['ref']) ({{ $move['ref'] }})@endif</td>
                            <td>{{ $move['journal_name'] ?? '' }}</td>
                            <td>@if($move['account_code']){{ $move['account_code'] }} @endif{{ $move['account_name'] ?? '' }}</td>
                            <td>{{ format_date($move['invoice_date']) }}</td>
                            <td>{{ format_date($move['invoice_date_due']) }}</td>
                            <td class="text-right">{{ $move['debit'] > 0 ? format_number($move['debit']) : '' }}</td>
                            <td class="text-right">{{ $move['credit'] > 0 ? format_number($move['credit']) : '' }}</td>
                            <td class="text-right">{{ format_number($runningBalance) }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach

            {{-- Totals --}}
            <tr class="total-row">
                <td>{{ __('accounting::filament/clusters/reporting.common.total') }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">{{ format_number($totalDebit) }}</td>
                <td class="text-right">{{ format_number($totalCredit) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="page-number"></div>
        <div>{{ __('accounting::filament/clusters/reporting/pages/partner-ledger.pdf.generated-on', ['date' => format_date(now(), 'LLL')]) }}</div>
    </div>
</body>
</html>
