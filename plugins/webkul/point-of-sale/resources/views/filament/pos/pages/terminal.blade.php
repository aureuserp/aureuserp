<x-filament-panels::page>
    @vite('plugins/webkul/point-of-sale/resources/js/till/main.js')

    <div
        id="pos-till"
        wire:ignore
        @if ($this->hasSession())
        data-boot="{{ json_encode($this->bootPayload()) }}"
        @endif
        data-config-id="{{ $this->config->id }}"
        data-access-token="{{ $this->config->access_token }}"
        data-sync-endpoint="{{ $this->syncEndpoint() }}"
        data-registers-endpoint="{{ $this->registersEndpoint() }}"
        data-registers="{{ json_encode($this->registers()) }}"
        data-open-endpoint="{{ $this->openEndpointBase() }}"
        data-boot-endpoint="{{ $this->bootEndpointBase() }}"
        data-discard-endpoint="{{ $this->discardEndpointBase() }}"
        data-back-office-url="{{ $this->backOfficeUrl() }}"
        data-logout-url="{{ $this->logoutUrl() }}"
        data-brand-logo="{{ $this->brandLogo() }}"
        data-user-name="{{ $this->userName() }}"
        data-user-avatar="{{ $this->userAvatar() }}"
        data-strings="{{ json_encode($this->strings()) }}"
    ></div>
</x-filament-panels::page>
