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

        .packing-slip {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .packing-slip:last-child {
            page-break-after: auto;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .left-info {
            width: 50%;
            float: left;
        }

        .right-info {
            width: 45%;
            float: right;
            text-align: right;
            border-left: 2px solid #f0f0f0;
            padding-left: 20px;
        }

        .clearfix {
            clear: both;
        }

        .slip-title {
            font-size: 24px;
            color: #1a4587;
            margin: 25px 0;
            padding: 15px 0;
            border-bottom: 2px solid #1a4587;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .barcode-container {
            display: inline-block;
            text-align: center;
            margin: 10px 0;
        }

        .barcode-text {
            font-size: 12px;
            margin-top: 5px;
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
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
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
        <div class="packing-slip">
            <!-- Header Section -->
            <div class="header">
                <div class="left-info">
                    <div style="font-size: 28px; color: #1a4587; margin-bottom: 10px;">{{ $record->company->name }}</div>

                    @if ($record->company->partner)
                        <div>
                            {{ $record->company->partner->street1 }}

                            @if ($record->company->partner->street2)
                                ,{{ $record->company->partner->street2 }}
                            @endif
                        </div>
                        
                        <div>
                            {{ $record->company->partner->city }},

                            @if ($record->company->partner->state)
                                {{ $record->company->partner->state->name }},
                            @endif
                            
                            {{ $record->company->partner->zip }}
                        </div>
                        
                        @if ($record->company->partner->country)
                            <div>
                                {{ $record->company->partner->country->name }}
                            </div>
                        @endif
                        
                        @if ($record->company->email)
                            <div>
                                Email: 
                                {{ $record->company->email }}
                            </div>
                        @endif
                        
                        @if ($record->company->phone)
                            <div>
                                Phone: 
                                {{ $record->company->phone }}
                            </div>
                        @endif
                    @endif
                </div>
                
                <div class="clearfix"></div>
            </div>
            
            <!-- Header Section -->
            <div class="header">
                @php
                    $address = $record->destinationLocation->warehouse?->partnerAddress ?? $record->sourceLocation->warehouse?->partnerAddress;
                @endphp

                @if ($address)
                    <div class="left-info">
                        <div style="font-weight: bold; margin-bottom: 15px;">Warehouse Address</div>
                        
                        <div style="margin-top: 15px;">
                            <div>
                                {{ $address->street1 }}

                                @if ($address->street2)
                                    ,{{ $address->street2 }}
                                @endif
                            </div>
                            
                            <div>
                                {{ $address->city }},

                                @if ($address->state)
                                    {{ $address->state->name }},
                                @endif
                                
                                {{ $address->zip }}
                            </div>
                            
                            @if ($address->country)
                                <div>
                                    {{ $address->country->name }}
                                </div>
                            @endif
                            
                            @if ($address->email)
                                <div>
                                    Email: 
                                    {{ $address->email }}
                                </div>
                            @endif
                            
                            @if ($address->phone)
                                <div>
                                    Phone: 
                                    {{ $address->phone }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                    
                @if($record->partner)
                    <div class="right-info">
                        <div style="font-weight: bold; margin-bottom: 15px;">Vendor Address</div>
                        
                        <div style="margin-top: 15px;">
                            <div>{{ $record->partner->name }}</div>
                            
                            <div>
                                {{ $record->partner->street1 }}

                                @if ($record->partner->street2)
                                    ,{{ $record->partner->street2 }}
                                @endif
                            </div>
                            
                            <div>
                                {{ $record->partner->city }},

                                @if ($record->partner->state)
                                    {{ $record->partner->state->name }},
                                @endif
                                
                                {{ $record->partner->zip }}
                            </div>
                            
                            @if ($record->partner->country)
                                <div>
                                    {{ $record->partner->country->name }}
                                </div>
                            @endif
                            
                            @if ($record->partner->email)
                                <div>
                                    Email: 
                                    {{ $record->partner->email }}
                                </div>
                            @endif
                            
                            @if ($record->partner->phone)
                                <div>
                                    Phone: 
                                    {{ $record->partner->phone }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <div class="clearfix"></div>
            </div>

            <!-- Slip Title -->
            <div class="slip-title">
                <span>Packing Slip #{{ $record->name }}</span>
                
                <div class="barcode-container">
                    {!! DNS1D::getBarcodeHTML($record->name, 'C128', 2, 44) !!}
                </div>
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <td width="50%">
                        <strong>Status:</strong><br>
                        {{ $record->state->name }}
                    </td>
                    
                    <td width="50%" style="text-align: right;">
                        <strong>Scheduled At:</strong><br>
                        {{ $record->scheduled_at }}
                    </td>
                </tr>
            </table>

            <!-- Items Table -->
            @if (! $record->moveLines->isEmpty())
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>To</th>
                            
                            @if (app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->enable_lots_serial_numbers)
                                <th>Lot/Serial Number</th>
                            @endif
                            
                            <th>Product Barcode</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($record->moveLines as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->qty) }} {{ $item->uom->name }}</td>
                                <td>
                                    {{ $item->destinationLocation->full_name }}
                                    
                                    @if ($item->result_package_id)
                                        - {{ $item->resultPackage->name }}
                                    @endif
                                </td>

                                @if (app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->enable_lots_serial_numbers)
                                    <td>
                                        @if ($item->lot)
                                        <div class="barcode-container">
                                            {!! DNS1D::getBarcodeHTML($item->lot->name, 'C128', 1.5, 33) !!}
                                            <div class="barcode-text">{{ $item->lot->name }}</div>
                                        </div>
                                        @endif
                                    </td>
                                @endif

                                <td>
                                    @if ($item->product->barcode)
                                        <div class="barcode-container">
                                            {!! DNS1D::getBarcodeHTML($item->product->barcode, 'C128', 1.5, 33) !!}
                                            <div class="barcode-text">{{ $item->product->barcode }}</div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="note">
                All items have been checked and packed according to quality standards.
            </div>
        </div>
    @endforeach
</body>
</html>