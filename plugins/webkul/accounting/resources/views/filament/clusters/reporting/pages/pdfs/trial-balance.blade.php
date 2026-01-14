<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trial Balance</title>
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
        th.text-center {
            text-align: center;
        }
        th.text-right {
            text-align: right;
        }
        td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            font-size: 11px;
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
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #333 !important;
        }
        .group-header {
            border-bottom: 1px solid #ccc;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Trial Balance</h2>
        <p>From {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}</p>
    </div>

    @if($data['accounts']->isEmpty())
        <div class="no-data">No accounts with transactions in this period</div>
    @else
        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="text-left">Account</th>
                    <th colspan="2" class="text-center group-header">Initial Balance</th>
                    <th colspan="2" class="text-center group-header">{{ \Carbon\Carbon::parse($data['date_from'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($data['date_to'])->format('d M Y') }}</th>
                    <th colspan="2" class="text-center group-header">End Balance</th>
                </tr>
                <tr>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
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
                    <td>Total</td>
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
</body>
</html>
