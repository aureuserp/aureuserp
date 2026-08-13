<x-filament-panels::page>
    <style>
        .coa-tree ul { list-style: none; margin: 0; padding: 0; }
        .coa-tree li { margin: 0; padding: 0; }
        .coa-tree button { line-height: 1.25; }
    </style>

    <div
        x-data="{
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
        class="flex flex-row items-start gap-4"
    >
        <aside
            x-cloak
            x-data="{
                w: parseInt(localStorage.getItem('coa_sidebar_w')) || 104,
                resize(e) {
                    const sx = e.clientX, sw = this.w;
                    const move = (ev) => {
                        this.w = Math.min(420, Math.max(88, sw + (ev.clientX - sx)));
                        localStorage.setItem('coa_sidebar_w', this.w);
                    };
                    const up = () => {
                        document.removeEventListener('mousemove', move);
                        document.removeEventListener('mouseup', up);
                        document.body.style.userSelect = '';
                    };
                    document.body.style.userSelect = 'none';
                    document.addEventListener('mousemove', move);
                    document.addEventListener('mouseup', up);
                },
            }"
            :style="`width:${w}px`"
            class="sticky top-4 shrink-0"
        >
            <nav
                class="relative overflow-hidden rounded-lg ring-1 ring-gray-950/5 bg-white transition-opacity dark:bg-gray-900 dark:ring-white/10"
                wire:loading.delay.shortest.class="opacity-60"
                wire:target="selectPrefix"
            >
                <div class="coa-tree max-h-[calc(100vh-12rem)] overflow-y-auto p-1 text-sm">
                    <button
                        type="button"
                        @click="pick(null)"
                        :class="selected === null
                            ? 'bg-primary-500/10 text-primary-700 dark:text-primary-300 font-medium'
                            : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5'"
                        class="flex w-full items-center gap-2 rounded-md px-2 py-1 text-start transition"
                    >
                        <x-heroicon-m-squares-2x2 class="h-4 w-4 shrink-0 text-gray-400" />
                        <span class="flex-1 truncate">{{ __('accounts::filament/resources/account/pages/manage-accounts.tree.all') }}</span>
                    </button>

                    <div class="my-1 h-px bg-gray-100 dark:bg-white/5"></div>

                    <ul role="tree">
                        @foreach($this->codeTree as $digit => $children)
                            @php($digit = (string) $digit)
                            @php($childCount = count($children))

                            <li role="treeitem" :aria-expanded="isExpanded('{{ $digit }}')">
                                <div
                                    class="flex items-center rounded-md transition"
                                    :class="selected === '{{ $digit }}' ? 'bg-primary-500/10' : 'hover:bg-gray-50 dark:hover:bg-white/5'"
                                >
                                    @if($childCount)
                                        <button
                                            type="button"
                                            @click="toggle('{{ $digit }}')"
                                            class="flex h-6 w-6 shrink-0 items-center justify-center text-gray-400 transition hover:text-gray-700 dark:hover:text-white"
                                        >
                                            <x-heroicon-m-chevron-right
                                                class="h-3.5 w-3.5 transition-transform duration-150"
                                                ::class="isExpanded('{{ $digit }}') ? 'rotate-90' : ''"
                                            />
                                        </button>
                                    @else
                                        <span class="h-6 w-6 shrink-0"></span>
                                    @endif

                                    <button
                                        type="button"
                                        @click="pick('{{ $digit }}')"
                                        class="flex min-w-0 flex-1 items-center py-1 pe-2 text-start font-mono transition"
                                        :class="selected === '{{ $digit }}'
                                            ? 'font-semibold text-primary-700 dark:text-primary-300'
                                            : 'text-gray-700 dark:text-gray-200'"
                                    >
                                        <span class="truncate">{{ $digit }}</span>
                                    </button>
                                </div>

                                @if($childCount)
                                    <ul
                                        style="margin-inline-start:0.85rem;padding-inline-start:0.5rem;border-inline-start:1px solid rgba(128,128,128,0.25);"
                                        role="group"
                                        x-show="isExpanded('{{ $digit }}')"
                                        x-collapse.duration.150ms
                                    >
                                        @foreach($children as $child)
                                            @php($child = (string) $child)
                                            <li role="treeitem">
                                                <button
                                                    type="button"
                                                    @click="pick('{{ $child }}')"
                                                    class="flex w-full items-center gap-1.5 rounded-md ps-2 pe-2 py-1 text-start font-mono transition"
                                                    :class="selected === '{{ $child }}'
                                                        ? 'bg-primary-500/10 font-semibold text-primary-700 dark:text-primary-300'
                                                        : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5'"
                                                >
                                                    <span class="truncate">{{ $child }}</span>
                                                    <x-heroicon-m-check
                                                        class="ms-auto h-3.5 w-3.5 shrink-0 text-primary-500"
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

                <div
                    @mousedown.prevent="resize($event)"
                    style="position:absolute;top:0;bottom:0;right:0;width:6px;cursor:col-resize;"
                    onmouseover="this.style.background='rgba(99,102,241,0.4)'"
                    onmouseout="this.style.background='transparent'"
                    title="Drag to resize"
                ></div>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            {{ $this->content }}
        </div>
    </div>
</x-filament-panels::page>
