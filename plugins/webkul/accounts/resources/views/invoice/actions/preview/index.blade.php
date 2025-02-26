<style>
    .invoice-container {
        background-color: white;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        margin: 0 auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .invoice-header {
        background-color: #f2f2f2;
        padding: 30px;
        position: relative;
    }

    .invoice-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .logo {
        font-size: 24px;
        font-weight: 700;
    }

    .invoice-number {
        font-size: 18px;
        font-weight: 600;
        background-color: #e6e6e6;
        padding: 8px 15px;
        border-radius: 30px;
    }

    .invoice-dates {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .date-item {
        flex: 1;
        margin-right: 20px;
    }

    .date-item:last-child {
        margin-right: 0;
    }

    .date-label {
        font-size: 12px;
        margin-bottom: 5px;
        opacity: 0.7;
    }

    .date-value {
        font-size: 16px;
        font-weight: 600;
    }

    .invoice-body {
        padding: 30px;
    }

    .address-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
    }

    .address-block {
        flex: 1;
        max-width: 45%;
    }

    .address-title {
        font-size: 14px;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .address-content {
        font-size: 15px;
        line-height: 1.5;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .invoice-table th {
        background-color: #f2f2f2;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #d9d9d9;
    }

    .invoice-table td {
        padding: 15px;
        border-bottom: 1px solid #d9d9d9;
        font-size: 15px;
    }

    .invoice-table .amount {
        font-weight: 600;
    }

    .total-section {
        width: 50%;
        margin-left: auto;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #d9d9d9;
    }

    .total-row.final {
        border-bottom: none;
        font-weight: 700;
        font-size: 18px;
        padding-top: 20px;
    }

    .payment-info {
        margin-top: 40px;
        padding: 20px;
        background-color: #f2f2f2;
        border-radius: 8px;
        font-size: 15px;
    }

    .payment-title {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .incoterm {
        display: inline-block;
        background-color: #e6e6e6;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }

    @media print {
        .invoice-container {
            box-shadow: none;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .address-container {
            flex-direction: column;
        }

        .address-block {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .total-section {
            width: 100%;
        }

        .invoice-dates {
            flex-direction: column;
        }

        .date-item {
            margin-bottom: 15px;
        }
    }
</style>

<div class="invoice-container">
    <div class="invoice-header">
        <div class="invoice-title">
            <div class="logo">{{ $record->company->name }}</div>
            <div class="invoice-number">{{ $record->name }}</div>
        </div>

        <div class="invoice-dates">
            @if ($record->invoice_date)
                <div class="date-item">
                    <div class="date-label">INVOICE DATE</div>
                    <div class="date-value">{{ $record->invoice_date }}</div>
                </div>
            @endif

            @if ($record->invoice_date_due)
                <div class="date-item">
                    <div class="date-label">DUE DATE</div>
                    <div class="date-value">{{ $record->invoice_date_due }}</div>
                </div>
            @endif

            @if ($record->invoiceIncoterm)
                <div class="date-item">
                    <div class="date-label">INCOTERM</div>
                    <div class="date-value">{{ $record->invoiceIncoterm?->name }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="invoice-body">
        <div class="address-container">
            <div class="address-block">
                <div class="address-title">From</div>
                <div class="address-content">
                    <strong>{{ $record->company->name }}</strong><br>
                    {{ sprintf(
                        "%s\n%s%s\n%s, %s %s\n%s",
                        $record->company->address->name ?? '',
                        $record->company->address->street1 ?? '',
                        $record->company->address->street2 ? ', ' . $record->company->address->street2 : '',
                        $record->company->address->city ?? '',
                        $record->company->address->state ? $record->company->address->state->name : '',
                        $record->company->address->zip ?? '',
                        $record->company->address->country ? $record->company->address->country->name : ''
                    ) }}
                </div>
            </div>

            <div class="address-block">
                <div class="address-title">To</div>
                <div class="address-content">
                    <strong>{{ $record->partner?->name }}</strong><br>

                    @if ($record->partner?->email)
                        {{ $record->partner?->email }}<br>
                    @endif

                    @if ($record->partner?->phone)
                        {{ $record->partner?->phone }}<br>
                    @endif

                    @php
                        $address = $record->partner->addresses->where('type', 'present')->first();

                        $address = sprintf(
                            "%s\n%s%s\n%s, %s %s\n%s",
                            $address->name ?? '',
                            $address->street1 ?? '',
                            $address->street2 ? ', ' . $address->street2 : '',
                            $address->city ?? '',
                            $address->state ? $address->state->name : '',
                            $address->zip ?? '',
                            $address->country ? $address->country->name : ''
                        );
                    @endphp

                    @if ($address)
                        {{ $address }}
                    @endif
                </div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="40%">Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->lines as $line)
                    <tr>
                        <td>{{ $line?->name ?? '-' }}</td>
                        <td>{{ $line?->quantity ?? '-' }}</td>
                        <td>{{ $record->currency->symbol }}{{ number_format($line->price_unit, 2) }}</td>
                        <td>{{ $record->currency->symbol }}{{ number_format($line->discount, 2) }}</td>
                        <td class="amount">{{ $record->currency->symbol }}{{ number_format($line->price_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div>Untaxed Amount</div>
                <div>{{ $record->currency->symbol }} {{ number_format($record->amount_untaxed, 2) }}</div>
            </div>

            <div class="total-row final">
                <div>TOTAL DUE</div>
                <div>{{ $record->currency->symbol }} {{ number_format($record->amount_total, 2) }}</div>
            </div>
        </div>

       @if ($record->name)
            <div class="payment-info">
                <div class="payment-title">Payment Information</div>
                <div>
                    Payment Communication: {{ $record->name }}
                    @if ($record?->partnerBank?->bank?->name || $record?->partnerBank?->account_number)
                        on this account:
                        {{ $record?->partnerBank?->bank?->name ?? 'N/A' }}
                        ({{ $record?->partnerBank?->account_number ?? 'N/A' }})
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
