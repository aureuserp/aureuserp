<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>General Ledger - {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}</title>
    <style>
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
        
        .account-header {
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
            content: "Page " counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>General Ledger</h1>
        <p>From {{ \Carbon\Carbon::parse($data['date_from'])->format('F j, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th>Date</th>
                <th>Communication</th>
                <th>Partner</th>
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

            @foreach($data['accounts'] as $account)
                @php
                    $totalDebit += $account->period_debit;
                    $totalCredit += $account->period_credit;
                    $isExpanded = in_array($account->id, $expandedAccounts ?? []);
                    $moves = $isExpanded ? $getAccountMoves($account->id) : [];
                    $runningBalance = $account->opening_balance;
                @endphp

                {{-- Account Header --}}
                <tr class="account-header">
                    <td>{{ $account->code }} {{ $account->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($account->period_debit, 2) }}</td>
                    <td class="text-right">{{ number_format($account->period_credit, 2) }}</td>
                    <td class="text-right">{{ number_format($account->ending_balance, 2) }}</td>
                </tr>

                {{-- Opening Balance --}}
                @if($account->opening_balance != 0 && $isExpanded)
                    <tr class="opening-balance">
                        <td class="move-row">Opening Balance</td>
                        <td>{{ \Carbon\Carbon::parse($data['date_from'])->format('m/d/Y') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($account->opening_balance, 2) }}</td>
                    </tr>
                @endif

                {{-- Move Lines --}}
                @if($isExpanded)
                    @foreach($moves as $move)
                    @php
                        $runningBalance += $move['debit'] - $move['credit'];
                    @endphp
                    <tr>
                        <td class="move-row">{{ $move['move_name'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($move['date'])->format('m/d/Y') }}</td>
                        <td>{{ $move['name'] ?? '' }}</td>
                        <td>{{ $move['partner_name'] ?? '' }}</td>
                        <td class="text-right">{{ $move['debit'] > 0 ? number_format($move['debit'], 2) : '' }}</td>
                        <td class="text-right">{{ $move['credit'] > 0 ? number_format($move['credit'], 2) : '' }}</td>
                        <td class="text-right">{{ number_format($runningBalance, 2) }}</td>
                    </tr>
                @endforeach
                @endif
            @endforeach

            {{-- Totals --}}
            <tr class="total-row">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="page-number"></div>
        <div>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</div>
    </div>
</body>
</html>
