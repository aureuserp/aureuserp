<x-filament-panels::page>
    @vite('plugins/webkul/point-of-sale/resources/js/till/main.js')

    <div
        id="pos-till"
        wire:ignore
        data-sync-endpoint="{{ $this->syncEndpoint() }}"
        data-registers-endpoint="{{ $this->registersEndpoint() }}"
        data-registers="{{ json_encode($this->registers()) }}"
        data-open-endpoint="{{ $this->openEndpointBase() }}"
        data-discard-endpoint="{{ $this->discardEndpointBase() }}"
        data-terminal-base="{{ $this->terminalBase() }}"
        data-back-office-url="{{ $this->backOfficeUrl() }}"
        data-logout-url="{{ $this->logoutUrl() }}"
        data-brand-logo="{{ $this->brandLogo() }}"
        data-user-name="{{ $this->userName() }}"
        data-user-avatar="{{ $this->userAvatar() }}"
        data-strings="{{ json_encode($this->strings()) }}"
    ></div>
</x-filament-panels::page>
