@php($prefix = 'point-of-sale::filament/admin/pages/registers.')

<x-filament-panels::page>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($this->getRegisters() as $register)
            @php($session = $this->liveSessionFor($register))
            @php($badge = $this->badgeFor($session))
            @php($lastClosed = $this->lastClosedSessionFor($register))
            @php($rescues = $this->rescueSessionCountFor($register))

            <x-filament::section>
                <div class="flex min-h-40 flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-xl font-bold text-gray-950 dark:text-white">{{ $register->name }}</p>

                            @if ($badge)
                                <span class="mt-1 inline-block" @if ($badge['tooltip']) title="{{ $badge['tooltip'] }}" @endif>
                                    <x-filament::badge :color="$badge['color']">{{ $badge['label'] }}</x-filament::badge>
                                </span>
                            @endif
                        </div>

                        <x-filament::dropdown placement="bottom-end">
                            <x-slot name="trigger">
                                <x-filament::icon-button
                                    icon="heroicon-m-ellipsis-vertical"
                                    color="gray"
                                    :label="__($prefix.'actions.more')"
                                />
                            </x-slot>

                            <div>
                                <x-filament::dropdown.header class="font-bold">
                                    {{ __($prefix.'dropdown.view') }}
                                </x-filament::dropdown.header>

                                <x-filament::dropdown.list>
                                    <x-filament::dropdown.list.item
                                        tag="a"
                                        :href="$this->ordersUrl($register)"
                                    >
                                        {{ __($prefix.'actions.orders') }}
                                    </x-filament::dropdown.list.item>

                                    <x-filament::dropdown.list.item
                                        tag="a"
                                        :href="$this->sessionsUrl($register)"
                                    >
                                        {{ __($prefix.'actions.sessions') }}
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </div>

                            <div>
                                <x-filament::dropdown.header class="font-bold">
                                    {{ __($prefix.'dropdown.reporting') }}
                                </x-filament::dropdown.header>

                                <x-filament::dropdown.list>
                                    <x-filament::dropdown.list.item
                                        tag="a"
                                        :href="$this->reportingUrl($register)"
                                    >
                                        {{ __($prefix.'actions.orders') }}
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </div>

                            <x-filament::dropdown.list>
                                <x-filament::dropdown.list.item
                                    tag="a"
                                    :href="$this->configUrl($register)"
                                >
                                    {{ __($prefix.'actions.edit') }}
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                    </div>

                    <div class="grid flex-auto grid-cols-2 items-start gap-4">
                        <div>
                            @if ($this->isClosing($session))
                                <x-filament::button
                                    color="gray"
                                    tag="a"
                                    :href="$this->sessionUrl($session)"
                                >
                                    {{ __($prefix.'actions.close') }}
                                </x-filament::button>
                            @else
                                <x-filament::button
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
                                    {{ $session
                                        ? __($prefix.'actions.continue')
                                        : __($prefix.'actions.open') }}
                                </x-filament::button>
                            @endif
                        </div>

                        <div class="flex flex-col gap-1 text-sm">
                            @if ($lastClosed?->stopped_at)
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __($prefix.'closing') }}</span>
                                    <span class="text-gray-950 dark:text-white">{{ $lastClosed->stopped_at->format('d/m/Y') }}</span>
                                </div>

                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __($prefix.'balance') }}</span>
                                    <span class="font-mono tabular-nums text-gray-950 dark:text-white">
                                        {{ money((float) $lastClosed->cash_balance_end_real, $register->currency?->name) }}
                                    </span>
                                </div>
                            @endif

                            @if ($rescues > 0)
                                <a
                                    href="{{ $this->sessionsUrl($register) }}"
                                    class="text-danger-600 hover:underline dark:text-danger-400"
                                >
                                    {{ trans_choice($prefix.'rescue-sessions', $rescues, ['count' => $rescues]) }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @if ($session?->user)
                        <div class="mt-auto flex justify-end">
                            <x-filament::avatar
                                :src="filament()->getUserAvatarUrl($session->user)"
                                :alt="$session->user->name"
                                size="sm"
                            />
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @empty
            <div class="sm:col-span-2 xl:col-span-3">
                <x-filament::section>
                    <x-filament::empty-state
                        icon="heroicon-o-computer-desktop"
                        :heading="__($prefix.'empty.heading')"
                        :description="__($prefix.'empty.description')"
                    />
                </x-filament::section>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
