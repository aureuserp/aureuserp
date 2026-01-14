<!DOCTYPE html>
<html>
<head>
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
        }
        h1 {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .date-range {
            font-size: 12px;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .section-header {
            font-weight: bold;
            font-size: 12px;
            padding: 8px 0;
            border-bottom: 2px solid #000;
            margin-top: 15px;
        }
        .account-line {
            padding: 4px 0 4px 40px;
            color: #666;
        }
        .total-line {
            padding: 8px 0 8px 0;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        .net-income {
            padding: 10px 0;
            font-weight: bold;
            font-size: 13px;
            border-bottom: 2px double #000;
            margin-top: 15px;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
        .empty-message {
            padding: 4px 0 4px 20px;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Profit & Loss Report</h1>
    <div class="date-range">
        From {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}
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
                        <td class="amount">{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="total-line">{{ $section['total_label'] }}</td>
                    <td class="total-line amount">{{ number_format($section['total'], 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="2" class="empty-message">{{ $section['empty_message'] }}</td>
                </tr>
            @endif

            <tr><td colspan="2" style="padding: 8px 0;"></td></tr>
        @endforeach

        <tr>
            <td class="net-income">{{ $data['is_profit'] ? 'Net Profit' : 'Net Loss' }}</td>
            <td class="net-income amount">{{ number_format(abs($data['net_income']), 2) }}</td>
        </tr>
    </table>
</body>
</html>
