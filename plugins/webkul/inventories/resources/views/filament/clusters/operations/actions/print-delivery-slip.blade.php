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

        .delivery-slip {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .delivery-slip:last-child {
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
        <div class="delivery-slip">
            <!-- Header Section -->
            <div class="header">
                <div class="left-info">
                    <div style="font-size: 28px; color: #1a4587; margin-bottom: 10px;">{{ $record->company->name }}</div>

                    @if ($record->company->address)
                        <div>
                            {{ $record->company->address->street1 }}

                            @if ($record->company->address->street2)
                                ,{{ $record->company->address->street2 }}
                            @endif
                        </div>
                        
                        <div>
                            {{ $record->company->address->city }},

                            @if ($record->company->address->state)
                                {{ $record->company->address->state->name }},
                            @endif
                            
                            {{ $record->company->address->zip }}
                        </div>
                        
                        @if ($record->company->address->country)
                            <div>
                                {{ $record->company->address->country->name }}
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
                <div class="left-info">
                </div>

                <div class="right-info">
                    <div style="font-weight: bold; margin-bottom: 15px;">Delivery Address</div>
                    
                    @if($record->partnerAddress)
                        <div style="margin-top: 15px;">
                            <div>
                                {{ $record->partnerAddress->name }}
                            </div>

                            <div>
                                {{ $record->partnerAddress->street1 }}

                                @if ($record->partnerAddress->street2)
                                    ,{{ $record->partnerAddress->street2 }}
                                @endif
                            </div>
                            
                            <div>
                                {{ $record->partnerAddress->city }},

                                @if ($record->partnerAddress->state)
                                    {{ $record->partnerAddress->state->name }},
                                @endif
                                
                                {{ $record->partnerAddress->zip }}
                            </div>
                            
                            @if ($record->partnerAddress->country)
                                <div>
                                    {{ $record->partnerAddress->country->name }}
                                </div>
                            @endif
                            
                            @if ($record->partnerAddress->email)
                                <div>
                                    Email: 
                                    {{ $record->partnerAddress->email }}
                                </div>
                            @endif
                            
                            @if ($record->partnerAddress->phone)
                                <div>
                                    Phone: 
                                    {{ $record->partnerAddress->phone }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="clearfix"></div>
            </div>

            <!-- Delivery Slip Title -->
            <div class="slip-title">
                Delivery Slip #{{ $record->name }}
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <td width="25%">
                        <strong>Shipping Date:</strong><br>
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

                            @if (app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->enable_lots_serial_numbers && app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->display_on_delivery_slips)
                                <th>Lot/Serial Number</th>
                            @endif

                            <th>Quantity</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($record->moveLines as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>

                                @if (app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->enable_lots_serial_numbers && app(\Webkul\Inventory\Settings\TraceabilitySettings::class)->display_on_delivery_slips)
                                    <td>{{ $item->lot?->name }}</td>
                                @endif
                                
                                <td>{{ number_format($item->qty) }} {{ $item->uom->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="note">
                Please inspect all items upon delivery. Report any discrepancies within 24 hours.
            </div>
        </div>
    @endforeach
</body>
</html>