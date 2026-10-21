<x-filament-panels::page>
    @vite('plugins/webkul/point-of-sale/resources/js/till/main.js')

    <div
        id="pos-till"
        wire:ignore
        data-boot="{{ json_encode($this->bootPayload()) }}"
        data-sync-endpoint="{{ $this->syncEndpoint() }}"
        data-access-token="{{ $this->config->access_token }}"
        data-back-office-url="{{ $this->backOfficeUrl() }}"
        data-logout-url="{{ $this->logoutUrl() }}"
        data-brand-logo="{{ $this->brandLogo() }}"
        data-user-name="{{ $this->userName() }}"
        data-user-avatar="{{ $this->userAvatar() }}"
    ></div>
</x-filament-panels::page>
