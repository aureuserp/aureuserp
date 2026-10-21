<x-filament-panels::page>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($this->getRegisters() as $register)
            @php($liveSession = $this->liveSessionFor($register))

            <x-filament::section>
                <x-slot name="heading">{{ $register->name }}</x-slot>

                <x-slot name="description">{{ $register->code }}</x-slot>

                <div class="flex items-center justify-between gap-3">
                    @if ($liveSession)
                        <x-filament::badge color="success" icon="heroicon-m-play">
                            {{ __('point-of-sale::filament/pos/pages/registers.session.open', ['name' => $liveSession->user?->name]) }}
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="gray" icon="heroicon-m-stop">
                            {{ __('point-of-sale::filament/pos/pages/registers.session.closed') }}
                        </x-filament::badge>
                    @endif

                    <div class="flex items-center gap-2">
                        @if ($liveSession && $this->isDiscardable($liveSession))
                            {{ ($this->discardSessionAction)(['session' => $liveSession->getKey()]) }}
                        @endif

                        <x-filament::button
                            icon="heroicon-m-play"
                            x-data="{
                                open(event) {
                                    if (event.ctrlKey || event.metaKey || event.button === 1) {
                                        const tab = window.open('', '_blank')

                                        $wire.openRegister({{ $register->getKey() }}, true).then((url) => {
                                            if (url) {
                                                tab.location.href = url
                                            } else {
                                                tab.close()
                                            }
                                        })

                                        return
                                    }

                                    $wire.openRegister({{ $register->getKey() }})
                                },
                            }"
                            x-on:click="open($event)"
                            x-on:auxclick.prevent="open($event)"
                        >
                            {{ $liveSession
                                ? __('point-of-sale::filament/pos/pages/registers.actions.resume')
                                : __('point-of-sale::filament/pos/pages/registers.actions.open') }}
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @empty
            <x-filament::empty-state
                icon="heroicon-o-computer-desktop"
                :heading="__('point-of-sale::filament/pos/pages/registers.empty.heading')"
                :description="__('point-of-sale::filament/pos/pages/registers.empty.description')"
            />
        @endforelse
    </div>
</x-filament-panels::page>
