<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - {{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            size: A4;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            color: #111827;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 12pt;
            color: #6b7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background-color: #f3f4f6;
            padding: 10px;
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
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section-header {
            background-color: #f9fafb;
            font-weight: bold;
            font-size: 12pt;
            color: #111827;
        }
        
        .subsection-header {
            background-color: #f3f4f6;
            padding-left: 30px !important;
            font-weight: 600;
            font-size: 11pt;
            color: #374151;
        }
        
        .account-row {
            padding-left: 50px !important;
            color: #4b5563;
        }
        
        .subsection-total {
            padding-left: 30px !important;
            font-weight: 600;
            background-color: #f9fafb;
            color: #1f2937;
        }
        
        .section-total {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 2px solid #9ca3af;
            color: #111827;
            font-size: 11.5pt;
        }
        
        .grand-total {
            font-weight: bold;
            background-color: #e5e7eb;
            border-top: 3px solid #6b7280;
            color: #111827;
            font-size: 12pt;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 11pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-number:after {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Balance Sheet</h1>
        <p>As of {{ \Carbon\Carbon::parse($data['date'])->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['sections'] as $section)
                {{-- Section Header --}}
                <tr class="section-header">
                    <td colspan="2">{{ $section['title'] }}</td>
                </tr>

                {{-- Subsections --}}
                @foreach($section['subsections'] as $subsection)
                    @php
                        $hasAccounts = count($subsection['accounts']) > 0;
                        $showSubsection = $hasAccounts || !isset($subsection['show_if_empty']) || $subsection['show_if_empty'];
                    @endphp

                    @if($showSubsection)
                        {{-- Subsection Header --}}
                        <tr class="subsection-header">
                            <td>{{ $subsection['title'] }}</td>
                            <td class="text-right"></td>
                        </tr>

                        {{-- Accounts --}}
                        @if($hasAccounts)
                            @foreach($subsection['accounts'] as $account)
                                <tr>
                                    <td class="account-row">
                                        {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                                    </td>
                                    <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
                                </tr>
                            @endforeach

                            {{-- Subsection Total --}}
                            <tr class="subsection-total">
                                <td>{{ $subsection['total_label'] }}</td>
                                <td class="text-right">{{ number_format($subsection['total'], 2) }}</td>
                            </tr>
                        @endif
                    @endif
                @endforeach

                {{-- Section Total --}}
                <tr class="section-total">
                    <td>{{ $section['total_label'] }}</td>
                    <td class="text-right">{{ number_format($section['total'], 2) }}</td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            <tr class="grand-total">
                <td>{{ $data['grand_total_label'] }}</td>
                <td class="text-right">{{ number_format($data['grand_total'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="page-number"></div>
        <div>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</div>
    </div>
</body>
</html>
