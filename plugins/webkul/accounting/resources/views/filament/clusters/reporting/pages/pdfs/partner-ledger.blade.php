<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Partner Ledger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            font-size: 11px;
        }
        .partner-header {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #000 !important;
        }
        .opening-balance {
            background-color: #fff;
            font-style: italic;
            color: #666;
        }
        .move-line {
            background-color: #fff;
            color: #666;
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
            color: #999;
        }
        .grand-total {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 1px solid #000 !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Partner Ledger</h2>
        <p>From {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}</p>
    </div>

    @if($data['partners']->isEmpty())
        <div class="no-data">No data available</div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="text-left">Partner</th>
                    <th class="text-left">Journal</th>
                    <th class="text-left">Account</th>
                    <th class="text-left">Invoice Date</th>
                    <th class="text-left">Due Date</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
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
                    @endphp

                    <tr class="partner-header">
                        <td>{{ $partner->name }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($partner->period_debit, 2) }}</td>
                        <td class="text-right">{{ number_format($partner->period_credit, 2) }}</td>
                        <td class="text-right">{{ number_format($partner->ending_balance, 2) }}</td>
                    </tr>

                    @if($partner->opening_balance != 0)
                        <tr class="opening-balance">
                            <td style="padding-left: 20px;">Opening Balance</td>
                            <td>{{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ $partner->opening_balance > 0 ? number_format($partner->opening_balance, 2) : '' }}</td>
                            <td class="text-right">{{ $partner->opening_balance < 0 ? number_format(abs($partner->opening_balance), 2) : '' }}</td>
                            <td class="text-right">{{ number_format($partner->opening_balance, 2) }}</td>
                        </tr>
                    @endif

                    @php
                        $runningBalance = $partner->opening_balance;
                    @endphp

                    @foreach($getPartnerMoves($partner->id) as $move)
                        @php
                            $runningBalance += ($move['debit'] - $move['credit']);
                        @endphp

                        <tr class="move-line">
                            <td style="padding-left: 20px;">{{ $move['move_name'] }}@if($move['ref']) ({{ $move['ref'] }})@endif</td>
                            <td>{{ $move['journal_name'] }}</td>
                            <td>@if($move['account_code']){{ $move['account_code'] }} {{ $move['account_name'] }}@endif</td>
                            <td>{{ \Carbon\Carbon::parse($move['invoice_date'])->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($move['invoice_date_due'])->format('M d, Y') }}</td>
                            <td class="text-right">{{ $move['debit'] > 0 ? number_format($move['debit'], 2) : '' }}</td>
                            <td class="text-right">{{ $move['credit'] > 0 ? number_format($move['credit'], 2) : '' }}</td>
                            <td class="text-right">{{ number_format($runningBalance, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr style="height: 8px;">
                        <td colspan="8" style="border: none; background: none;"></td>
                    </tr>
                @endforeach

                <tr class="grand-total">
                    <td>Total Partner Ledger</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                    <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
