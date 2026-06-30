<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('accounting::filament/clusters/reporting.pages.trial-balance.navigation.title') }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            size: A4 landscape;
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
        
        .header h2 {
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
            margin-top: 10px;
        }
        
        th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11pt;
            color: #374151;
        }
        
        th.text-center {
            text-align: center;
        }
        
        th.text-right {
            text-align: right;
        }
        
        td {
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            font-size: 11pt;
            color: #4b5563;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
        }
        
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
            border-top: 2px solid #9ca3af !important;
            color: #111827;
        }
        
        .group-header {
            border-bottom: 2px solid #d1d5db;
            background-color: #f9fafb;
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
        <h2>{{ __('accounting::filament/clusters/reporting.pages.trial-balance.navigation.title') }}</h2>
        <p>From {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}</p>
    </div>

    @if($data['accounts']->isEmpty())
        <div class="no-data">{{ __('accounting::filament/clusters/reporting.common.no-accounts-transactions') }}</div>
    @else
        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="text-left">{{ __('accounting::filament/clusters/reporting.common.account') }}</th>
                    <th colspan="2" class="text-center group-header">{{ __('accounting::filament/clusters/reporting.common.initial-balance') }}</th>
                    <th colspan="2" class="text-center group-header">{{ \Carbon\Carbon::parse($data['date_from'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($data['date_to'])->format('d M Y') }}</th>
                    <th colspan="2" class="text-center group-header">{{ __('accounting::filament/clusters/reporting.common.end-balance') }}</th>
                </tr>
                <tr>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.debit') }}</th>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.credit') }}</th>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.debit') }}</th>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.credit') }}</th>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.debit') }}</th>
                    <th class="text-right">{{ __('accounting::filament/clusters/reporting.common.credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['accounts'] as $account)
                    <tr>
                        <td class="text-left">{{ $account->code ? $account->code . ' ' : '' }}{{ $account->name }}</td>
                        <td class="text-right">{{ $account->initial_debit > 0 ? number_format($account->initial_debit, 2) : '0.00' }}</td>
                        <td class="text-right">{{ $account->initial_credit > 0 ? number_format($account->initial_credit, 2) : '0.00' }}</td>
                        <td class="text-right">{{ $account->period_debit > 0 ? number_format($account->period_debit, 2) : '0.00' }}</td>
                        <td class="text-right">{{ $account->period_credit > 0 ? number_format($account->period_credit, 2) : '0.00' }}</td>
                        <td class="text-right">{{ $account->end_debit > 0 ? number_format($account->end_debit, 2) : '0.00' }}</td>
                        <td class="text-right">{{ $account->end_credit > 0 ? number_format($account->end_credit, 2) : '0.00' }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td>{{ __('accounting::filament/clusters/reporting.common.total') }}</td>
                    <td class="text-right">{{ number_format($data['totals']['initial_debit'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['initial_credit'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['period_debit'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['period_credit'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['end_debit'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['end_credit'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div>Generated on {{ now()->format('F j, Y \\a\\t g:i A') }}</div>
    </div>
</body>
</html>
