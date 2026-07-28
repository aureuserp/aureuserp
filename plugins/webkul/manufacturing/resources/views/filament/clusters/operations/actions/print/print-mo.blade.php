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

        .mo-slip {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .mo-slip:last-child {
            page-break-after: auto;
        }

        .header-bar {
            width: 100%;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 6px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #555555;
        }

        .header-bar table {
            width: 100%;
        }

        .header-bar td {
            padding: 0;
            vertical-align: middle;
        }

        .slip-title {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 20px 0 10px 0;
        }

        .barcode-container {
            display: inline-block;
            text-align: center;
        }

        .barcode-text {
            font-size: 11px;
            margin-top: 4px;
            color: #333333;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .meta-label {
            font-weight: bold;
            width: 140px;
        }

        .product-info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .product-info-table td {
            padding: 4px 20px 4px 0;
            vertical-align: top;
        }

        .product-info-table .label {
            font-weight: bold;
            display: block;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #333333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .items-table th {
            background: #ffffff;
            border: 1px solid #cccccc;
            padding: 10px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }

        .items-table td {
            padding: 10px 12px;
            border: 1px solid #cccccc;
            font-size: 13px;
        }

        .items-table tr:nth-child(even) td {
            background: #f8f8f8;
        }

        .text-right {
            text-align: right;
        }

        .footer-bar {
            width: 100%;
            border-top: 1px solid #cccccc;
            padding-top: 6px;
            margin-top: 30px;
            font-size: 11px;
            color: #666666;
        }

        .footer-bar table {
            width: 100%;
        }

        .footer-bar td {
            padding: 0;
            vertical-align: middle;
        }

        .clearfix::after {
            content: '';
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="mo-slip">
        {{-- Header bar --}}
        <div class="header-bar">
            <table>
                <tr>
                    <td>{{ now()->format('Y-m-d H:i') }}</td>
                    <td style="text-align: center;">{{ $record->company?->name ?? '' }}</td>
                    <td style="text-align: right;">1 / 1</td>
                </tr>
            </table>
        </div>

        {{-- Title + barcode --}}
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: bottom; padding: 0;">
                    <div class="slip-title">{{ $record->name }}</div>
                </td>
                <td style="text-align: right; vertical-align: bottom; padding: 0; width: 200px;">
                    @if ($record->name)
                        <div class="barcode-container">
                            {!! DNS1D::getBarcodeHTML($record->name, 'C128', 1.5, 40) !!}
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Responsible --}}
        @if ($record->assignedUser)
            <table class="meta-table">
                <tr>
                    <td class="meta-label">{{ __('manufacturing::app.documents.responsible') }}</td>
                    <td>{{ $record->assignedUser->name }}</td>
                </tr>
            </table>
        @endif

        {{-- Product / Qty to produce / Qty producing --}}
        <table class="product-info-table">
            <tr>
                <td>
                    <span class="label">{{ __('manufacturing::app.documents.product') }}</span>
                    {{ $record->product?->name ?? '—' }}
                </td>
                <td>
                    <span class="label">{{ __('manufacturing::app.documents.quantity-to-produce') }}</span>
                    {{ number_format((float) $record->quantity, 2) }} {{ $record->uom?->name ?? '' }}
                </td>
                @if ($record->quantity_producing !== null)
                    <td>
                        <span class="label">{{ __('manufacturing::app.documents.quantity-producing') }}</span>
                        {{ number_format((float) $record->quantity_producing, 2) }} {{ $record->uom?->name ?? '' }}
                    </td>
                @endif
            </tr>
        </table>

        {{-- Work Orders / Operations Done --}}
        @if ($record->workOrders->isNotEmpty())
            <div class="section-title">{{ __('manufacturing::app.documents.operations-done') }}</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('manufacturing::app.documents.operation') }}</th>
                        <th>{{ __('manufacturing::app.documents.work-center') }}</th>
                        <th>{{ __('manufacturing::app.documents.duration-minutes') }}</th>
                        <th>{{ __('manufacturing::app.documents.barcode') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->workOrders as $workOrder)
                        <tr>
                            <td>{{ $workOrder->name ?? $workOrder->operation?->name ?? '—' }}</td>
                            <td>{{ $workOrder->workCenter?->name ?? '—' }}</td>
                            <td>{{ number_format((float) ($workOrder->duration ?? 0), 1) }}</td>
                            <td>
                                @if ($workOrder->name)
                                    <div class="barcode-container">
                                        {!! DNS1D::getBarcodeHTML($workOrder->name, 'C128', 1.2, 30) !!}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Components --}}
        @if ($record->rawMaterialMoves->isNotEmpty())
            <div class="section-title">{{ __('manufacturing::app.documents.components') }}</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('manufacturing::app.documents.product') }}</th>
                        <th class="text-right">{{ __('manufacturing::app.documents.consumed') }}</th>
                        <th class="text-right">{{ __('manufacturing::app.documents.to-consume') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->rawMaterialMoves as $move)
                        <tr>
                            <td>{{ $move->product?->name ?? '—' }}</td>
                            <td class="text-right">{{ number_format((float) ($move->quantity ?? 0), 2) }}</td>
                            <td class="text-right">
                                {{ number_format((float) $move->product_uom_qty, 2) }}
                                {{ $move->uom?->name ?? '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Footer bar --}}
        <div class="footer-bar">
            <table>
                <tr>
                    <td>{{ now()->format('Y-m-d H:i') }}</td>
                    <td style="text-align: center;">{{ $record->company?->name ?? '' }}</td>
                    <td style="text-align: right;">1 / 1</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
