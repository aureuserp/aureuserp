<div>
    @if ($companies->count() > 1)
        <x-filament::dropdown placement="bottom-end" width="xs" teleport>
            <x-slot name="trigger">
                <x-filament::link tag="button" size="sm" color="gray" style="text-decoration:none;">
                    <span style="display:inline-flex;align-items:center;gap:0.375rem;">
                        <x-filament::icon icon="heroicon-o-building-office-2" style="width:1.25rem;height:1.25rem;flex-shrink:0;" />

                        <span>{{ $companies->firstWhere('id', $active[0] ?? null)?->name }}</span>

                        <x-filament::icon icon="heroicon-m-chevron-down" style="width:1rem;height:1rem;flex-shrink:0;" />
                    </span>
                </x-filament::link>
            </x-slot>

            @php
                $byParent = $companies->groupBy('parent_id');
                $ids = $companies->pluck('id')->all();

                $flatten = function ($company, int $depth) use (&$flatten, $byParent) {
                    $rows = [['company' => $company, 'depth' => $depth]];

                    foreach ($byParent->get($company->id, collect()) as $child) {
                        $rows = array_merge($rows, $flatten($child, $depth + 1));
                    }

                    return $rows;
                };

                $ordered = [];

                foreach ($companies as $company) {
                    if (! in_array($company->parent_id, $ids, true)) {
                        $ordered = array_merge($ordered, $flatten($company, 0));
                    }
                }
            @endphp

            <form method="POST" action="{{ route('company-context.set') }}" x-data>
                @csrf

                <div style="display:flex;flex-direction:column;gap:0.125rem;padding:0.5rem;max-height:18rem;overflow-y:auto;">
                    @foreach ($ordered as $row)
                        @php($company = $row['company'])
                        @php($depth = $row['depth'])
                        <div
                            @click="
                                const boxes = [...$root.querySelectorAll('input[type=checkbox]')];
                                const me = $el.querySelector('input[type=checkbox]');
                                if (boxes.filter(c => c.checked).length <= 1) {
                                    boxes.forEach(c => c.checked = false);
                                }
                                me.checked = true;
                                const current = document.createElement('input');
                                current.type = 'hidden';
                                current.name = 'current';
                                current.value = me.value;
                                $root.prepend(current);
                                $root.requestSubmit();
                            "
                            onmouseover="this.style.background='rgba(128,128,128,0.14)'"
                            onmouseout="this.style.background='transparent'"
                            style="display:flex;align-items:center;gap:0.625rem;padding:0.5rem 0.625rem;padding-left:{{ 0.625 + $depth * 1.25 }}rem;border-radius:0.5rem;font-size:0.875rem;cursor:pointer;transition:background .1s;"
                        >
                            @if ($depth > 0)
                                <span style="color:rgba(128,128,128,0.6);flex-shrink:0;font-size:0.75rem;line-height:1;">└</span>
                            @endif

                            <x-filament::input.checkbox
                                name="companies[]"
                                value="{{ $company->id }}"
                                :checked="in_array($company->id, $active)"
                                x-on:click.stop
                            />

                            <span style="flex:1;{{ $company->id === ($active[0] ?? null) ? 'font-weight:600;' : '' }}">{{ $company->name }}</span>
                        </div>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;padding:0.5rem;border-top:1px solid rgba(128,128,128,0.2);">
                    <x-filament::button type="submit" size="sm" style="width:100%;justify-content:center;">
                        {{ __('support::company-switcher.confirm') }}
                    </x-filament::button>

                    <x-filament::button type="submit" name="action" value="reset" color="gray" size="sm" style="width:100%;justify-content:center;">
                        {{ __('support::company-switcher.reset') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::dropdown>
    @endif
</div>
