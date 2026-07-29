<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
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
                    <td style="text-align: center;">
                        <div class="record-name">{{ $record->name }}</div>

                        <div class="package-info">{{ $record->product->name }}</div>
                        
                        <div class="package-info">Qty: {{ $record->qty.' '.$record->product->uom->name }}</div>
                        
                        @if ($record->barcode)
                            <div class="barcode-container">
                                {!! DNS1D::getBarcodeHTML($record->barcode, 'C128', 1, 30) !!}

                                <div class="barcode-text">{{ $record->barcode }}</div>
                            </div>
                        @endif
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