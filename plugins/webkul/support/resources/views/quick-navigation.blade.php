@php
    $cmdSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3"/></svg>';
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
            class="fi-quick-nav"
        >
            <div class="fi-quick-nav-backdrop" x-on:click="dismiss()"></div>

            <div
                x-trap.noscroll="open"
                class="fi-quick-nav-panel"
            >
                <div class="fi-quick-nav-header">
                    @if ($parentLabel)
                        <button
                            type="button"
                            wire:click="back()"
                            class="fi-quick-nav-back"
                        >
                            <x-filament::icon icon="heroicon-o-chevron-left" />
                            <span>{{ $parentLabel }}</span>
                        </button>
                        <span class="shrink-0 text-gray-300 dark:text-gray-600">/</span>
                    @else
                        <x-filament::icon
                            wire:loading.remove
                            wire:target="query"
                            icon="heroicon-o-magnifying-glass"
                            class="fi-quick-nav-search-icon"
                        />

                        <x-filament::loading-indicator wire:loading wire:target="query" class="fi-quick-nav-search-icon" />
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
                        class="fi-quick-nav-input"
                    />

                    <kbd class="fi-quick-nav-key">esc</kbd>
                </div>

                <div x-ref="results" class="fi-quick-nav-results" wire:key="quick-navigation-results">
                    @php $index = 0; @endphp

                    @forelse ($this->groups as $group)
                        <div class="fi-quick-nav-group-label">{{ $group['label'] }}</div>

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
                                x-bind:class="{ 'fi-active': active === {{ $index }} }"
                                class="fi-quick-nav-item"
                            >
                                <span class="fi-quick-nav-item-icon">
                                    @if (! empty($item['icon']))
                                        {!! $item['icon'] !!}
                                    @else
                                        <x-filament::icon icon="heroicon-o-chevron-right" />
                                    @endif
                                </span>

                                <span class="fi-quick-nav-item-text">
                                    <span class="fi-quick-nav-item-label">{{ $item['title'] }}</span>

                                    @if (! empty($item['subtitle']))
                                        <span class="fi-quick-nav-item-description">{{ $item['subtitle'] }}</span>
                                    @endif
                                </span>

                                @if (! empty($item['canFavorite']))
                                    <button
                                        type="button"
                                        data-favorite-toggle
                                        wire:click.stop="toggleFavorite(@js($item['url']), @js($item['title']))"
                                        x-on:click.stop
                                        title="{{ ! empty($item['favorite']) ? __('support::quick-navigation.unpin') : __('support::quick-navigation.pin') }}"
                                        @class([
                                            'fi-quick-nav-item-pin',
                                            'fi-pinned' => ! empty($item['favorite']),
                                        ])
                                    >
                                        <x-filament::icon :icon="! empty($item['favorite']) ? 'heroicon-s-star' : 'heroicon-o-star'" />
                                    </button>
                                @endif

                                @if (! empty($item['cluster']))
                                    <x-filament::icon icon="heroicon-o-chevron-right" class="fi-quick-nav-item-end" />
                                @else
                                    <x-filament::icon icon="heroicon-m-arrow-turn-down-left" class="fi-quick-nav-item-end" />
                                @endif
                            </div>

                            @php $index++; @endphp
                        @endforeach
                    @empty
                        <div class="fi-quick-nav-empty">
                            {{ trim($this->query) === '' ? __('support::quick-navigation.hint') : __('support::quick-navigation.empty') }}
                        </div>
                    @endforelse
                </div>

                <div class="fi-quick-nav-footer">
                    <span class="fi-quick-nav-hint">
                        <kbd class="fi-quick-nav-key"><x-filament::icon icon="heroicon-m-arrow-up" /></kbd>
                        <kbd class="fi-quick-nav-key"><x-filament::icon icon="heroicon-m-arrow-down" /></kbd>
                        <span>{{ __('support::quick-navigation.navigate') }}</span>
                    </span>
                    <span class="fi-quick-nav-hint">
                        <kbd class="fi-quick-nav-key"><x-filament::icon icon="heroicon-m-arrow-turn-down-left" /></kbd>
                        <span>{{ __('support::quick-navigation.open') }}</span>
                    </span>
                    <span class="fi-quick-nav-hint">
                        <kbd class="fi-quick-nav-key">
                            <span x-show="isMac" class="inline-flex">{!! $cmdSvg !!}</span>
                            <span x-show="! isMac">Ctrl</span>
                        </kbd>
                        <kbd class="fi-quick-nav-key"><x-filament::icon icon="heroicon-m-arrow-turn-down-left" /></kbd>
                        <span>{{ __('support::quick-navigation.new_tab_hint') }}</span>
                    </span>
                    <span class="fi-quick-nav-hint">
                        <kbd class="fi-quick-nav-key">
                            <span x-show="isMac" class="inline-flex">{!! $cmdSvg !!}</span>
                            <span x-show="! isMac">Ctrl</span>
                        </kbd>
                        <kbd class="fi-quick-nav-key">S</kbd>
                        <span>{{ __('support::quick-navigation.pin') }}</span>
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
