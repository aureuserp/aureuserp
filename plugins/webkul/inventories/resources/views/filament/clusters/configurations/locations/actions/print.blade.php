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
            vertical-align: top;
            padding: 12px;
            border: 1px solid #e9ecef;
            background: white;
            overflow: hidden;
            page-break-inside: avoid;
        }

        td.empty {
            border: none;
        }

        .record-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a4587;
            text-transform: uppercase;
            margin-bottom: 5px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .barcode-container {
            margin: 8px 0;
            text-align: center;
            display: inline-block;
        }

        .barcode-text {
            font-size: 11px;
            color: #333;
            margin-top: 4px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <table>
        @foreach ($records->chunk(4) as $chunk)
            <tr>
                @foreach ($chunk as $record)
                    <td style="text-align: center;">
                        <div class="record-name">
                            {{ strtoupper($record->name) }}
                        </div>
                        
                        @if ($record->barcode)
                            <div class="barcode-container">
                                {!! DNS1D::getBarcodeHTML($record->barcode, 'C128', 1, 30) !!}

                                <div class="barcode-text">{{ $record->barcode }}</div>
                            </div>
                        @endif
                    </td>
                @endforeach

                @for ($i = $chunk->count(); $i < 4; $i++)
                    <td class="empty"></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>