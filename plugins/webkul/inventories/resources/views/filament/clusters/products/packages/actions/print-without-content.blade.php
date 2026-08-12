<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 15px;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            table-layout: fixed;
        }

        td {
            width: 33%;
            vertical-align: top;
            padding: 12px;
            border: 1px solid #e9ecef;
            background: white;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .empty {
            border: none;
            background: transparent;
        }

        .record-name {
            font-size: 14px;
            font-weight: bold;
            color: #1a4587;
            text-transform: uppercase;
            margin-bottom: 6px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .package-info {
            font-size: 11px;
            color: #666;
            margin: 4px 0;
            text-align: center;
        }

        .barcode-container {
            margin: 12px 0 8px;
            text-align: center;
            display: inline-block;
        }

        .barcode-text {
            font-size: 12px;
            color: #333;
            margin-top: 6px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <table>
        @foreach ($records->chunk(3) as $chunk)
            <tr>
                @foreach ($chunk as $record)
                    <td style="text-align: center">
                        <div class="record-name">{{ strtoupper($record->name) }}</div>

                        @if ($record->packageType)
                            <div class="package-info">Package Type: {{ $record->packageType->name }}</div>
                        @endif

                        @if ($record->pack_date)
                            <div class="package-info">Pack Date: {{ $record->pack_date }}</div>
                        @endif
                        
                        <div class="barcode-container">
                            {!! DNS1D::getBarcodeHTML($record->name, 'C128', 1.5, 33) !!}
                            <div class="barcode-text">{{ $record->name }}</div>
                        </div>
                    </td>
                @endforeach
                
                @for ($i = $chunk->count(); $i < 3; $i++)
                    <td class="empty"></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>