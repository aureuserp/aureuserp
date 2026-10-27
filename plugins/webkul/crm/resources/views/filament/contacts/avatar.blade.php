@php
    $size ??= 'md';
    $isCompany = $contact->account_type === \Webkul\Partner\Enums\AccountType::COMPANY;
@endphp

<span class="crm-contact-avatar crm-contact-avatar-{{ $size }}">
    @if ($contact->avatar_url)
        <img src="{{ $contact->avatar_url }}" alt="{{ $contact->name }}" loading="lazy" />
    @else
        <x-filament::icon :icon="$isCompany ? 'heroicon-o-building-office' : 'heroicon-o-user'" class="crm-contact-avatar-icon" />
    @endif
</span>
