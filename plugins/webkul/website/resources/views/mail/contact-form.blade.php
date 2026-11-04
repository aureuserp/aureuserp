<div style="font-family: Arial, sans-serif; font-size: 14px; color: #111827;">
    <p>{{ __('website::filament/customer/contact-us.mail.intro') }}</p>

    <table cellpadding="6" style="border-collapse: collapse;">
        @foreach (['name', 'phone', 'email', 'company', 'subject'] as $field)
            <tr>
                <td style="font-weight: bold; vertical-align: top;">{{ __('website::filament/customer/contact-us.fields.'.$field) }}</td>
                <td>{{ $data[$field] ?? '—' }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; vertical-align: top;">{{ __('website::filament/customer/contact-us.fields.question') }}</td>
            <td>{!! nl2br(e($data['question'])) !!}</td>
        </tr>
    </table>
</div>
