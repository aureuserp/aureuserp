@php
    $kbd = 'inline-flex h-6 min-w-6 items-center justify-center rounded-md border border-black/10 bg-gray-100 px-1.5 text-[11px] font-semibold leading-none text-gray-600 dark:border-white/10! dark:bg-zinc-800! dark:text-zinc-300!';
    $kbdIcon = 'h-3.5 w-3.5';
    $cmdSvg = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3"/></svg>';
@endphp

<div
    wire:ignore.self
    x-data="{
        open: false,
        active: 0,
        isMac: /mac|iphone|ipad|ipod/i.test(navigator.userAgent),
        logoutUrl: @js(\Filament\Facades\Filament::getLogoutUrl()),
        csrf: @js(csrf_token()),
        toggle() {
            this.open = ! this.open;

            if (this.open) {
                this.active = 0;
                this.$nextTick(() => {
                    this.$refs.input?.focus();
                    if (this.$refs.results) this.$refs.results.scrollTop = 0;
                });
            }
        },
        close() {
            this.open = false;
        },
        dismiss() {
            this.open = false;
            this.$wire.resetState();
        },
        handleEscape() {
            if (this.$wire.parent) {
                this.$wire.back();
            } else {
                this.dismiss();
            }
        },
        items() {
            return Array.from(this.$refs.results?.querySelectorAll('[data-quick-nav-item]') ?? []);
        },
        move(direction) {
            const items = this.items();

            if (! items.length) {
                return;
            }

            this.active = (this.active + direction + items.length) % items.length;
            items[this.active]?.scrollIntoView({ block: 'nearest' });
        },
        select() {
            const items = this.items();

            if (! items.length) {
                return;
            }

            items[Math.min(this.active, items.length - 1)]?.click();
        },
        selectNewTab() {
            const items = this.items();

            if (! items.length) {
                return;
            }

            const el = items[Math.min(this.active, items.length - 1)];
            const url = el?.getAttribute('data-url');

            if (url) {
                window.open(url, '_blank');
                this.close();
            } else {
                el?.click();
            }
        },
        run(action) {
            if (action === 'toggle-theme') {
                this.toggleTheme();
            } else if (action === 'logout') {
                this.logout();
            } else if (action === 'copy-url') {
                navigator.clipboard?.writeText(window.location.href);
            } else if (action === 'open-new-tab') {
                window.open(window.location.href, '_blank');
            }
        },
        toggleTheme() {
            const current = window.Alpine?.store('theme')
                ?? (document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            const next = current === 'dark' ? 'light' : 'dark';

            window.dispatchEvent(new CustomEvent('theme-changed', { detail: next }));
        },
        toggleFavoriteActive() {
            const items = this.items();

            if (! items.length) {
                return;
            }

            const el = items[Math.min(this.active, items.length - 1)];

            el?.querySelector('[data-favorite-toggle]')?.click();
        },
        logout() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.logoutUrl;

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = this.csrf;

            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        },
    }"
    x-on:keydown.window.meta.k.prevent="toggle()"
    x-on:keydown.window.ctrl.k.prevent="toggle()"
    x-on:keydown.window.ctrl.shift.k.prevent="toggle()"
    x-on:keydown.window.ctrl.slash.prevent="toggle()"
    x-on:open-quick-navigation.window="toggle()"
    x-on:quick-navigation-reset.window="active = 0; $refs.results && ($refs.results.scrollTop = 0); $nextTick(() => $refs.input?.focus())"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition.opacity
            x-on:keydown.escape.window="handleEscape()"
            style="display: none;"
            class="fixed inset-0 z-50 flex items-start justify-center px-4 pb-4 pt-[12vh]"
        >
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" x-on:click="dismiss()"></div>

            <div
                x-trap.noscroll="open"
                class="relative flex max-h-[70vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-2xl dark:border-white/10! dark:bg-zinc-900!"
            >
                <div class="flex items-center gap-3 border-b border-black/5 px-4 py-3.5 dark:border-white/10!">
                    @if ($parentLabel)
                        <button
                            type="button"
                            wire:click="back()"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-blue-50 px-2 py-1 text-sm font-medium text-blue-600 dark:bg-blue-500/15! dark:text-blue-300!"
                        >
                            <x-filament::icon icon="heroicon-o-chevron-left" class="h-4 w-4" />
                            <span>{{ $parentLabel }}</span>
                        </button>
                        <span class="shrink-0 text-gray-300 dark:text-zinc-600!">/</span>
                    @else
                        <x-filament::icon
                            wire:loading.remove
                            wire:target="query"
                            icon="heroicon-o-magnifying-glass"
                            class="h-5 w-5 shrink-0 text-gray-400"
                        />

                        <x-filament::loading-indicator wire:loading wire:target="query" class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    @endif

                    <input
                        x-ref="input"
                        type="text"
                        wire:model.live.debounce.400ms="query"
                        x-on:input="active = 0; $refs.results && ($refs.results.scrollTop = 0)"
                        x-on:keydown.down.prevent="move(1)"
                        x-on:keydown.up.prevent="move(-1)"
                        x-on:keydown.ctrl.enter.prevent="selectNewTab()"
                        x-on:keydown.meta.enter.prevent="selectNewTab()"
                        x-on:keydown.ctrl.s.prevent="toggleFavoriteActive()"
                        x-on:keydown.meta.s.prevent="toggleFavoriteActive()"
                        x-on:keydown.enter.prevent="select()"
                        placeholder="{{ __('support::quick-navigation.placeholder') }}"
                        autocomplete="off"
                        class="flex-1 border-0 bg-transparent p-0 text-base text-gray-900 shadow-none outline-none placeholder:text-gray-400 focus:border-0 focus:outline-none focus:ring-0 dark:text-zinc-100!"
                    />

                    <kbd class="{{ $kbd }}">esc</kbd>
                </div>

                <div x-ref="results" class="overflow-y-auto p-1.5" wire:key="quick-navigation-results">
                    @php $index = 0; @endphp

                    @forelse ($this->groups as $group)
                        <div class="px-2.5 pb-1.5 pt-2.5 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-zinc-500!">
                            {{ $group['label'] }}
                        </div>

                        @foreach ($group['items'] as $item)
                            <div
                                role="button"
                                tabindex="-1"
                                data-quick-nav-item
                                @if (! empty($item['url'])) data-url="{{ $item['url'] }}" @endif
                                wire:key="quick-navigation-item-{{ $item['id'] }}"
                                @if (! empty($item['action']))
                                    x-on:click="run(@js($item['action']))"
                                @elseif (! empty($item['cluster']))
                                    wire:click="drill(@js($item['cluster']), @js($item['title']), @js($group['label']))"
                                @else
                                    wire:click="goto(@js($item['url']), @js($item['title']))"
                                    x-on:click="close()"
                                @endif
                                x-on:mouseenter="active = {{ $index }}"
                                :class="active === {{ $index }} ? 'is-active bg-blue-50 dark:bg-blue-500/15!' : ''"
                                class="group flex w-full cursor-pointer items-center gap-3 rounded-lg px-2.5 py-2.5 text-left text-gray-900 dark:text-zinc-100!"
                            >
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 group-[.is-active]:bg-blue-100 group-[.is-active]:text-blue-600 dark:bg-zinc-800! dark:text-zinc-400! dark:group-[.is-active]:bg-blue-500/25! dark:group-[.is-active]:text-blue-300! [&>svg]:h-[1.15rem] [&>svg]:w-[1.15rem]">
                                    @if (! empty($item['icon']))
                                        {!! $item['icon'] !!}
                                    @else
                                        <x-filament::icon icon="heroicon-o-chevron-right" class="h-[1.15rem] w-[1.15rem]" />
                                    @endif
                                </span>

                                <span class="flex min-w-0 flex-1 flex-col">
                                    <span class="truncate text-sm font-medium">{{ $item['title'] }}</span>

                                    @if (! empty($item['subtitle']))
                                        <span class="truncate text-xs text-gray-400 dark:text-zinc-500!">{{ $item['subtitle'] }}</span>
                                    @endif
                                </span>

                                @if (! empty($item['canFavorite']))
                                    <button
                                        type="button"
                                        data-favorite-toggle
                                        wire:click.stop="toggleFavorite(@js($item['url']), @js($item['title']))"
                                        x-on:click.stop
                                        title="{{ ! empty($item['favorite']) ? __('support::quick-navigation.unpin') : __('support::quick-navigation.pin') }}"
                                        class="shrink-0 rounded p-1 {{ ! empty($item['favorite']) ? 'text-amber-500' : 'text-gray-400 opacity-0 hover:text-amber-500 group-hover:opacity-100 group-[.is-active]:opacity-100 dark:text-zinc-500!' }}"
                                    >
                                        <x-filament::icon :icon="! empty($item['favorite']) ? 'heroicon-s-star' : 'heroicon-o-star'" class="h-4 w-4" />
                                    </button>
                                @endif

                                @if (! empty($item['cluster']))
                                    <x-filament::icon icon="heroicon-o-chevron-right" class="h-4 w-4 shrink-0 text-gray-400 group-[.is-active]:text-blue-500 dark:text-zinc-500!" />
                                @else
                                    <x-filament::icon icon="heroicon-m-arrow-turn-down-left" class="h-4 w-4 shrink-0 text-gray-400 group-[.is-active]:text-blue-500 dark:text-zinc-500!" />
                                @endif
                            </div>

                            @php $index++; @endphp
                        @endforeach
                    @empty
                        <div class="px-4 py-9 text-center text-sm text-gray-400 dark:text-zinc-500!">
                            {{ trim($this->query) === '' ? __('support::quick-navigation.hint') : __('support::quick-navigation.empty') }}
                        </div>
                    @endforelse
                </div>

                <div class="flex items-center gap-5 border-t border-black/5 px-4 py-3 text-xs text-gray-500 dark:border-white/10! dark:text-zinc-400!">
                    <span class="inline-flex items-center gap-1">
                        <kbd class="{{ $kbd }}"><x-filament::icon icon="heroicon-m-arrow-up" class="{{ $kbdIcon }}" /></kbd>
                        <kbd class="{{ $kbd }}"><x-filament::icon icon="heroicon-m-arrow-down" class="{{ $kbdIcon }}" /></kbd>
                        <span class="ml-1">{{ __('support::quick-navigation.navigate') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="{{ $kbd }}"><x-filament::icon icon="heroicon-m-arrow-turn-down-left" class="{{ $kbdIcon }}" /></kbd>
                        <span class="ml-1">{{ __('support::quick-navigation.open') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="{{ $kbd }}">
                            <span x-show="isMac" class="inline-flex">{!! $cmdSvg !!}</span>
                            <span x-show="! isMac">Ctrl</span>
                        </kbd>
                        <kbd class="{{ $kbd }}"><x-filament::icon icon="heroicon-m-arrow-turn-down-left" class="{{ $kbdIcon }}" /></kbd>
                        <span class="ml-1">{{ __('support::quick-navigation.new_tab_hint') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="{{ $kbd }}">
                            <span x-show="isMac" class="inline-flex">{!! $cmdSvg !!}</span>
                            <span x-show="! isMac">Ctrl</span>
                        </kbd>
                        <kbd class="{{ $kbd }}">S</kbd>
                        <span class="ml-1">{{ __('support::quick-navigation.pin') }}</span>
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
