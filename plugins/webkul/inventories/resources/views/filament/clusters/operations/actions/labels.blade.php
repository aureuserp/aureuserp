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
            vertical-align: top;
            padding: 12px;
            border: 1px solid #e9ecef;
            background: white;
            overflow: hidden;
            page-break-inside: avoid;
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

        .price {
            font-size: 12px;
            font-weight: bold;
            color: #1a4587;
            text-align: center;
            margin-top: 6px;
        }

        /* Column specific styles */
        .format-4x7_price td,
        .format-4x12 td,
        .format-4x12_price td {
            width: 22%;
            min-height: 80px;
        }

        .format-2x7_price td {
            width: 47%;
            min-height: 120px;
        }

        .format-dymo table {
            border-spacing: 5px;
        }
        
        .format-dymo td {
            padding: 8px;
            min-height: 60px;
        }
    </style>
</head>

<body class="format-{{ $format }}">
    @php
        $columns = match ($format) {
            'dymo' => 1,
            '2x7_price' => 2,
            '4x7_price' => 4,
            '4x12' => 4,
            '4x12_price' => 4,
            default => 2,
        };

        if ($type == 'lot') {
            $records = $records->filter(fn ($moveLine) => $moveLine->lot_id);
        }
        
        $showPrice = str_contains($format, 'price');
        
        $flatRecords = [];

        foreach ($records as $record) {
            $quantity = match ($quantityType) {
                'operation' => $record->qty,
                'custom' => $quantity,
                'per_lot' => 1,
                default => $record->qty,
            };

            for ($i = 0; $i < $quantity; $i++) {
                $flatRecords[] = $record;
            }
        }

        $totalRecords = count($flatRecords);
        $barcodeScale = $columns == 4 ? 1.2 : 2;
    @endphp

    <table>
        @for ($i = 0; $i < $totalRecords; $i += $columns)
            <tr>
                @for ($j = 0; $j < $columns && ($i + $j) < $totalRecords; $j++)
                    @php
                        $currentRecord = $flatRecords[$i + $j];
                        $barcode = $currentRecord->lot?->name ?? $currentRecord->product->barcode;
                    @endphp

                    <td style="text-align: center;">
                        <div class="record-name">
                            {{ strtoupper($currentRecord->product->name) }}
                        </div>

                        @if ($barcode)
                            <div class="barcode-container">
                                {!! DNS1D::getBarcodeHTML($barcode, 'C128', $barcodeScale, 33) !!}
                                
                                <div class="barcode-text">{{ $barcode }}</div>
                            </div>
                        @endif

                        @if ($showPrice)
                            <div class="price">
                                {{ number_format($currentRecord->price, 2) }}
                            </div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>