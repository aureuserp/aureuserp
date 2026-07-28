<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
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
            'dymo'       => 1,
            '2x7_price'  => 2,
            '4x7_price'  => 4,
            '4x12'       => 4,
            '4x12_price' => 4,
            default      => 2,
        };

        $showPrice = str_contains($format, 'price');

        // When MO is done, print label for finished product; otherwise print labels for components
        if ($isDone) {
            $items = collect();

            if ($record->product) {
                $qty = match ($quantityType) {
                    'custom'    => $quantity,
                    'operation' => max(1, (int) round((float) $record->quantity_producing ?: $record->quantity)),
                    default     => 1,
                };

                for ($i = 0; $i < $qty; $i++) {
                    $items->push([
                        'product' => $record->product,
                        'barcode' => $record->product->barcode,
                        'price'   => $record->product->price ?? 0,
                    ]);
                }
            }
        } else {
            $items = collect();

            foreach ($record->rawMaterialMoves as $move) {
                if (! $move->product) {
                    continue;
                }

                $qty = match ($quantityType) {
                    'custom'    => $quantity,
                    'operation' => max(1, (int) round((float) $move->product_uom_qty)),
                    default     => 1,
                };

                for ($i = 0; $i < $qty; $i++) {
                    $items->push([
                        'product' => $move->product,
                        'barcode' => $move->product->barcode,
                        'price'   => $move->product->price ?? 0,
                    ]);
                }
            }
        }

        $barcodeScale = $columns === 4 ? 1.2 : 2;
        $totalItems = count($items);
    @endphp

    <table>
        @for ($i = 0; $i < $totalItems; $i += $columns)
            <tr>
                @for ($j = 0; $j < $columns && ($i + $j) < $totalItems; $j++)
                    @php
                        $item = $items[$i + $j];
                    @endphp

                    <td style="text-align: center;">
                        <div class="record-name">
                            {{ strtoupper($item['product']->name) }}
                        </div>

                        @if ($item['barcode'])
                            <div class="barcode-container">
                                {!! DNS1D::getBarcodeHTML($item['barcode'], 'C128', $barcodeScale, 33) !!}
                                <div class="barcode-text">{{ $item['barcode'] }}</div>
                            </div>
                        @endif

                        @if ($showPrice)
                            <div class="price">
                                {{ number_format((float) ($item['price'] ?? 0), 2) }}
                            </div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>
