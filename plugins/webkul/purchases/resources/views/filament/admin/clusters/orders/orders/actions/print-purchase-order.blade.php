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

        .purchase-order {
            margin-bottom: 50px;
            page-break-after: always;
        }

        .purchase-order:last-child {
            page-break-after: auto;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .company-info {
            width: 50%;
            float: left;
        }

        .vendor-info {
            width: 45%;
            float: right;
            text-align: right;
            border-left: 2px solid #f0f0f0;
            padding-left: 20px;
        }

        .clearfix {
            clear: both;
        }

        .po-number {
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

        .summary-table {
            width: 300px;
            float: right;
            margin-top: 20px;
            background: #f8f9fa;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .total-row {
            font-weight: bold;
            border-top: 2px solid #1a4587;
        }

        .terms {
            margin-top: 40px;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
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
        <div class="purchase-order">
            <!-- Header Section -->
            <div class="header">
                <div class="company-info">
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

                <div class="vendor-info">
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
                <div class="clearfix"></div>
            </div>

            <!-- PO Number -->
            <div class="po-number">
                Purchase Order #{{ $record->name }}
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    @if ($record->user_id)
                        <td width="25%">
                            <strong>Buyer</strong><br>
                            {{ $record->user->name }}
                        </td>
                    @endif
                    
                    @if ($record->partner_reference)
                        <td width="25%">
                            <strong>Order Reference</strong><br>
                            {{ $record->partner_reference }}
                        </td>
                    @endif

                    @if ($record->ordered_at)
                        <td width="25%">
                            <strong>Order Deadline</strong><br>
                            {{ $record->ordered_at }}
                        </td>
                    @endif

                    @if ($record->planned_at)
                        <td width="25%">
                            <strong>Expected Arrival</strong><br>
                            {{ $record->planned_at }}
                        </td>
                    @endif
                </tr>
            </table>

            <!-- Items Table -->
            @if (! $record->lines->isEmpty())
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Taxes</th>
                            <th>Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($record->lines as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->product_qty.' '.$item->uom->name }}</td>
                                <td>{{ money($item->price_unit, $record->currency->name) }}</td>
                                <td>{{ round($item->discount, 2) }}%</td>
                                <td>{{ $item->taxes->pluck('name')->implode(', ') }}</td>
                                <td>{{ money($item->price_subtotal, $record->currency->name) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Summary Table -->
                <table class="summary-table">
                    <tr>
                        <td>Untaxed Amount</td>
                        <td style="text-align: right;">{{ money($record->untaxed_amount, $record->currency->name) }}</td>
                    </tr>

                    <tr>
                        <td>Tax</td>
                        <td style="text-align: right;">{{ money($record->tax_amount, $record->currency->name) }}</td>
                    </tr>

                    <tr class="total-row">
                        <td>Total</td>
                        <td style="text-align: right;">{{ money($record->total_amount, $record->currency->name) }}</td>
                    </tr>
                </table>
                
                <div class="clearfix"></div>
            @endif

            @if ($record->payment_term_id)
                <div class="terms">
                    <strong>Payment Terms:</strong><br>
                    {{ $record->paymentTerm->name }}
                </div>
            @endif

            <div class="note">
                Thank you for your business! Please contact us if you have any questions.
            </div>
        </div>
    @endforeach
</body>
</html>