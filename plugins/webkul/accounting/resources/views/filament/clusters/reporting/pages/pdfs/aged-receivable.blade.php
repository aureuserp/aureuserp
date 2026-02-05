<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aged Receivable - {{ $asOfDate }}</title>
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
            font-weight: 500;
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
        <h1>Aged Receivable</h1>
        <p>As of {{ \Carbon\Carbon::parse($asOfDate)->format('F j, Y') }}</p>
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
                    
                    @if(in_array($partner['id'], $expandedPartners ?? []) && isset($partnerLines[$partner['id']]))
                        @foreach($partnerLines[$partner['id']] as $line)
                            <tr class="line-row">
                                <td style="padding-left: 20px; font-weight: 400;">{{ $line['move_name'] }}</td>
                                <td>{{ $line['at_date'] != 0 ? number_format($line['at_date'], 2) : '' }}</td>
                                <td>{{ $line['period_1'] != 0 ? number_format($line['period_1'], 2) : '' }}</td>
                                <td>{{ $line['period_2'] != 0 ? number_format($line['period_2'], 2) : '' }}</td>
                                <td>{{ $line['period_3'] != 0 ? number_format($line['period_3'], 2) : '' }}</td>
                                <td>{{ $line['period_4'] != 0 ? number_format($line['period_4'], 2) : '' }}</td>
                                <td>{{ $line['older'] != 0 ? number_format($line['older'], 2) : '' }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                <tr class="total-row">
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

    <div class="footer">
        <div class="page-number"></div>
        <div>Generated on {{ now()->format('F j, Y \\a\\t g:i A') }}</div>
    </div>
</body>
</html>
