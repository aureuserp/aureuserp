@php
    $company = $this->getCompany();
    $settings = app(\Webkul\Website\Settings\ContactSettings::class);
    $phone = $company?->phone ?: $settings->phone;
    $email = $company?->email ?: $settings->email;
    $address = collect([$company?->street1, $company?->street2, trim(($company?->zip ?? '').' '.($company?->city ?? '')), $company?->country?->name])->filter()->implode(', ');
@endphp

<x-filament-panels::page dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="website-contact-us">
        <div>
            <p class="website-contact-us-intro">
                {{ __('website::filament/customer/contact-us.intro') }}<br>
                {{ __('website::filament/customer/contact-us.intro-reply') }}
            </p>

            <form wire:submit="submit" class="website-contact-us-form">
                {{ $this->form }}

                <div class="website-contact-us-actions">
                    <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="submit">
                        {{ __('website::filament/customer/contact-us.submit') }}
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if ($company)
            <aside class="website-contact-us-company">
                <h3>{{ $company->name }}</h3>

                <ul>
                    @if (filled($address))
                        <li><x-filament::icon icon="heroicon-m-map-pin" class="website-contact-us-icon" /> <span>{{ $address }}</span></li>
                    @endif

                    @if (filled($phone))
                        <li><x-filament::icon icon="heroicon-m-phone" class="website-contact-us-icon" /> <a href="tel:{{ $phone }}">{{ $phone }}</a></li>
                    @endif

                    @if (filled($email))
                        <li><x-filament::icon icon="heroicon-m-envelope" class="website-contact-us-icon" /> <a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    @endif
                </ul>
            </aside>
        @endif
    </div>

    <style>
        .website-contact-us { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 4rem; align-items: start; }
        .website-contact-us-intro { margin-bottom: 1.25rem; line-height: 1.6; color: rgb(55 65 81); }
        .dark .website-contact-us-intro { color: rgb(209 213 219); }
        .website-contact-us-form { position: relative; display: flex; flex-direction: column; gap: 1.25rem; }
        .website-contact-us-actions { display: flex; justify-content: flex-end; }
        .website-contact-us-company h3 { font-size: 1.15rem; font-weight: 600; margin-bottom: 0.5rem; }
        .website-contact-us-company ul { display: flex; flex-direction: column; gap: 0.35rem; }
        .website-contact-us-company li { display: flex; align-items: flex-start; gap: 0.6rem; }
        .website-contact-us-icon { width: 1rem; height: 1rem; margin-top: 0.2rem; flex-shrink: 0; }
        @media (max-width: 768px) { .website-contact-us { grid-template-columns: minmax(0, 1fr); gap: 2rem; } }
    </style>
</x-filament-panels::page>
