<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333333;
            line-height: 1.6;
            margin: 0;
        }

        .package-content {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .package-content:last-child {
            page-break-after: auto;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .package-title {
            font-size: 24px;
            color: #1a4587;
            margin: 25px 0;
            padding: 15px 0;
            border-bottom: 2px solid #1a4587;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .barcode-container {
            text-align: center;
            margin: 10px 0;
            display: inline-block;
        }

        .barcode-text {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 10px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table th {
            background: #1a4587;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .lot-info {
            background: #fff;
            padding: 10px;
            border-left: 3px solid #1a4587;
            margin: 5px 0;
        }

        .note {
            color: #666;
            font-size: 0.9em;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    @foreach ($records as $record)
        <div class="package-content">
            <!-- Package Header -->
            <div class="package-title">
                <span>{{ $record->name }}</span>
                <div class="barcode-container">
                    {!! DNS1D::getBarcodeHTML($record->name, 'C128', 1.5, 33) !!}
                    <div class="barcode-text">{{ $record->name }}</div>
                </div>
            </div>

            <!-- Package Details -->
            @if ($record->package_type_id || $record->pack_date)
                <table class="details-table">
                    <tr>
                        @if ($record->package_type_id)
                            <td width="50%">
                                <strong>{{ __('inventories::app.documents.package-type') }}</strong><br>
                                {{ $record->packageType->name }}
                            </td>
                        @endif

                        @if ($record->pack_date)
                            <td width="50%">
                                <strong>{{ __('inventories::app.documents.pack-date') }}</strong><br>
                                {{ $record->pack_date }}
                            </td>
                        @endif
                    </tr>
            </table>
            @endif

            <!-- Items Table -->
            @if (! $record->quantities->isEmpty())
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>{{ __('inventories::app.documents.barcode') }}</th>
                            <th>{{ __('inventories::app.documents.product') }}</th>
                            <th>{{ __('inventories::app.documents.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->quantities as $item)
                            <tr>
                                <td>
                                    @if ($item->product->barcode)
                                        <div class="barcode-container">
                                            {!! DNS1D::getBarcodeHTML($item->product->barcode, 'C128', 1, 30) !!}
                                            
                                            <div class="barcode-text">{{ $item->product->barcode }}</div>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->quantity) }} {{ $item->product->uom->name }}</td>
                            </tr>

                            @if (app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->enable_lots_serial_numbers && $item->lot)
                                <tr>
                                    <td colspan="3">
                                        <div class="lot-info">
                                            <div style="margin-bottom: 8px">
                                                <strong>{{ __('inventories::app.documents.lot-serial') }}</strong> {{ $item->lot->name }}
                                            </div>
                                            
                                            <div class="barcode-container">
                                                {!! DNS1D::getBarcodeHTML($item->lot->name, 'C128', 1.5, 33) !!}
                                                
                                                <div class="barcode-text">{{ $item->lot->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
            </table>
            @endif

            <div class="note">
                Verify contents against packing list before accepting delivery.
            </div>
        </div>
    @endforeach
</body>
</html>
