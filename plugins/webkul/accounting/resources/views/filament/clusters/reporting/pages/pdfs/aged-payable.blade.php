<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aged Payable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            padding: 8px;
            text-align: right;
            font-weight: bold;
        }
        th:first-child {
            text-align: left;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        td:first-child {
            text-align: left;
        }
        .grand-total {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Aged Payable</h2>
        <p>As of {{ $asOfDate }}</p>
    </div>

    @if(empty($partners))
        <div class="no-data">No data available</div>
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
                    <th>Partner</th>
                    <th>Not due</th>
                    <th>1-{{ $period }}</th>
                    <th>{{ $period + 1 }}-{{ $period * 2 }}</th>
                    <th>{{ ($period * 2) + 1 }}-{{ $period * 3 }}</th>
                    <th>{{ ($period * 3) + 1 }}-{{ $period * 4 }}</th>
                    <th>{{ $period * 4 }}+</th>
                    <th>Total</th>
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
                        <td>{{ number_format($partner['at_date'], 2) }}</td>
                        <td>{{ number_format($partner['period_1'], 2) }}</td>
                        <td>{{ number_format($partner['period_2'], 2) }}</td>
                        <td>{{ number_format($partner['period_3'], 2) }}</td>
                        <td>{{ number_format($partner['period_4'], 2) }}</td>
                        <td>{{ number_format($partner['older'], 2) }}</td>
                        <td>{{ number_format($partner['total'], 2) }}</td>
                    </tr>
                @endforeach

                <tr class="grand-total">
                    <td>Total</td>
                    <td>{{ number_format($totalAtDate, 2) }}</td>
                    <td>{{ number_format($totalPeriod1, 2) }}</td>
                    <td>{{ number_format($totalPeriod2, 2) }}</td>
                    <td>{{ number_format($totalPeriod3, 2) }}</td>
                    <td>{{ number_format($totalPeriod4, 2) }}</td>
                    <td>{{ number_format($totalOlder, 2) }}</td>
                    <td>{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
