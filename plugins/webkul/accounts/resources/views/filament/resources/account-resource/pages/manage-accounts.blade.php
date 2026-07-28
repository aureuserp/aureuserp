<x-filament-panels::page>
    <div
        x-data="{
            search: '',
            selected: @js($selectedCodePrefix),
            expanded: {@if($selectedCodePrefix) @js((string) substr($selectedCodePrefix, 0, 1)): true @endif},
            isExpanded(k) { return !! this.expanded[k] },
            toggle(k) { this.expanded[k] = ! this.expanded[k] },
            pick(prefix) {
                this.selected = prefix;
                if (prefix !== null) this.expanded[String(prefix).charAt(0)] = true;
                $wire.selectPrefix(prefix);
            },
        }"
        class="flex flex-row items-start gap-6"
    >
        <aside x-cloak class="sticky top-4 w-40 shrink-0">
            <nav
                class="overflow-hidden rounded-2xl bg-white shadow-sm transition-opacity dark:bg-gray-900/60 dark:backdrop-blur"
                wire:loading.delay.shortest.class="opacity-60"
                wire:target="selectPrefix"
            >
                <div class="max-h-[calc(100vh-12rem)] overflow-y-auto p-2">
                    {{-- All --}}
                    <button
                        type="button"
                        @click="pick(null)"
                        x-show="!search"
                        :class="selected === null
                            ? 'bg-gradient-to-r from-primary-500/10 to-primary-500/5 text-primary-700 dark:text-primary-300'
                            : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5'"
                        class="group mb-1 flex w-full items-center gap-2 rounded-lg px-3 py-2 text-start text-sm font-medium transition"
                    >
                        <x-heroicon-m-squares-2x2
                            class="h-4 w-4 shrink-0"
                            ::class="selected === null
                                ? 'text-primary-600 dark:text-primary-400'
                                : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'"
                        />
                        <span class="flex-1">
                            {{ __('accounts::filament/resources/account/pages/manage-accounts.tree.all') }}
                        </span>
                        <span x-show="selected === null" class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                    </button>

                    <div x-show="!search" class="my-2 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-white/10"></div>

                    <ul class="space-y-0.5" role="tree">
                        @foreach($this->codeTree as $digit => $children)
                            @php($digit = (string) $digit)
                            @php($childCount = count($children))
                            @php($childListJs = implode(',', array_map('strval', (array) $children)))

                            <li
                                role="treeitem"
                                :aria-expanded="isExpanded('{{ $digit }}')"
                                x-show="!search || '{{ $digit }}'.includes(search) || '{{ $childListJs }}'.includes(search)"
                            >
                                <div
                                    class="group flex items-center rounded-lg transition"
                                    :class="selected === '{{ $digit }}'
                                        ? 'bg-primary-500/10'
                                        : 'hover:bg-gray-50 dark:hover:bg-white/5'"
                                >
                                    <button
                                        type="button"
                                        @click="toggle('{{ $digit }}')"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:text-gray-700 dark:hover:text-white"
                                        :aria-label="isExpanded('{{ $digit }}') ? 'Collapse {{ $digit }}' : 'Expand {{ $digit }}'"
                                    >
                                        <x-heroicon-m-chevron-right
                                            class="h-4 w-4 transition-transform duration-200"
                                            ::class="isExpanded('{{ $digit }}') ? 'rotate-90' : ''"
                                        />
                                    </button>

                                    <button
                                        type="button"
                                        @click="pick('{{ $digit }}')"
                                        class="flex min-w-0 flex-1 items-center gap-2 py-1.5 pe-2 text-start text-sm transition"
                                        :class="selected === '{{ $digit }}'
                                            ? 'font-semibold text-primary-700 dark:text-primary-300'
                                            : 'font-medium text-gray-700 dark:text-gray-200'"
                                    >
                                        <span class="relative h-4 w-4 shrink-0">
                                            <x-heroicon-m-folder
                                                class="absolute inset-0 h-4 w-4"
                                                x-show="!isExpanded('{{ $digit }}')"
                                                ::class="selected === '{{ $digit }}'
                                                    ? 'text-primary-500'
                                                    : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400'"
                                            />
                                            <x-heroicon-m-folder-open
                                                class="absolute inset-0 h-4 w-4 text-primary-500"
                                                x-show="isExpanded('{{ $digit }}')"
                                                x-cloak
                                            />
                                        </span>

                                        <span class="truncate font-mono tracking-tight">{{ $digit }}</span>

                                        @if($childCount)
                                            <span
                                                class="ms-auto inline-flex min-w-[1.25rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums transition"
                                                :class="selected === '{{ $digit }}'
                                                    ? 'bg-primary-500/15 text-primary-700 dark:text-primary-300'
                                                    : 'bg-gray-100 text-gray-600 group-hover:bg-gray-200 dark:bg-white/5 dark:text-gray-400 dark:group-hover:bg-white/10'"
                                            >
                                                {{ $childCount }}
                                            </span>
                                        @endif
                                    </button>
                                </div>

                                @if($childCount)
                                    <ul
                                        class="relative ms-4 mt-0.5 space-y-0.5 ps-3"
                                        role="group"
                                        x-show="isExpanded('{{ $digit }}')"
                                        x-collapse.duration.150ms
                                    >
                                        <span
                                            class="absolute start-0 top-0 bottom-2 w-px bg-gradient-to-b from-gray-200 via-gray-200 to-transparent dark:from-white/10 dark:via-white/10"
                                            aria-hidden="true"
                                        ></span>

                                        @foreach($children as $child)
                                            @php($child = (string) $child)

                                            <li
                                                role="treeitem"
                                                class="relative"
                                                x-show="!search || '{{ $child }}'.includes(search)"
                                            >
                                                <span
                                                    class="absolute start-[-12px] top-1/2 h-px w-3 bg-gray-200 dark:bg-white/10"
                                                    aria-hidden="true"
                                                ></span>

                                                <button
                                                    type="button"
                                                    @click="pick('{{ $child }}')"
                                                    class="group/child flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-start text-sm transition"
                                                    :class="selected === '{{ $child }}'
                                                        ? 'bg-primary-500/10 font-semibold text-primary-700 dark:text-primary-300'
                                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white'"
                                                >
                                                    <span
                                                        class="h-1.5 w-1.5 shrink-0 rounded-full transition"
                                                        :class="selected === '{{ $child }}'
                                                            ? 'bg-primary-500'
                                                            : 'bg-gray-300 group-hover/child:bg-gray-400 dark:bg-white/20 dark:group-hover/child:bg-white/40'"
                                                    ></span>
                                                    <span class="font-mono tracking-tight">{{ $child }}</span>
                                                    <x-heroicon-m-check
                                                        class="ms-auto h-3.5 w-3.5 text-primary-500"
                                                        x-show="selected === '{{ $child }}'"
                                                        x-cloak
                                                    />
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            {{ $this->content }}
        </div>
    </div>
</x-filament-panels::page>
