@php
    $config = [
        'modules'       => $matrix['modules'],
        'allNames'      => $matrix['allNames'],
        'abilityLabels' => $matrix['abilityLabels'],
        'statePath'     => $getStatePath(),
        'initial'       => $getState() ?? [],
        'disabled'      => $isDisabled(),
    ];
@endphp

<div
    x-data="{
        cfg: @js($config),
        selected: {},
        active: null,
        q: '',
        _t: null,
        init() {
            (this.cfg.initial || []).forEach((n) => (this.selected[n] = true));
            this.active = this.cfg.modules.length ? this.cfg.modules[0].key : null;
        },
        get modules() { return this.cfg.modules; },
        get disabled() { return this.cfg.disabled; },
        get current() { return this.modules.find((m) => m.key === this.active) || null; },
        filteredModules() {
            const q = this.q.trim().toLowerCase();
            return q ? this.modules.filter((m) => m.label.toLowerCase().includes(q)) : this.modules;
        },
        isOn(n) { return this.selected[n] === true; },
        abilityLabel(a) { return this.cfg.abilityLabels[a] || a; },
        colNames(m, col) { return m.resources.filter((r) => r.abilities[col]).map((r) => r.abilities[col]); },
        pushState() {
            clearTimeout(this._t);
            this._t = setTimeout(() => {
                const names = Object.keys(this.selected).filter((n) => this.selected[n]);
                this.$wire.$set(this.cfg.statePath, names, false);
            }, 60);
        },
        setMode(m) { this.$wire.$set('data.permissions_sync_mode', m, false); },
        toggle(n) { this.selected[n] = ! this.selected[n]; this.setMode('manual'); this.pushState(); },
        setMany(names, val, mode) { names.forEach((n) => (this.selected[n] = val)); if (mode) this.setMode(mode); this.pushState(); },
        colSet(m, col, val) { this.setMany(this.colNames(m, col), val, 'manual'); },
        selectAllGlobal() { this.cfg.allNames.forEach((n) => (this.selected[n] = true)); this.setMode('all'); this.pushState(); },
        deselectAllGlobal() { this.cfg.allNames.forEach((n) => (this.selected[n] = false)); this.setMode('none'); this.pushState(); },
        grantedIn(m) { return m.names.filter((n) => this.isOn(n)).length; },
        grantedTotal() { return this.cfg.allNames.filter((n) => this.isOn(n)).length; },
        total() { return this.cfg.allNames.length; },
        countClass(m) { return this.grantedIn(m) === m.names.length && m.names.length ? 'text-success-600 font-semibold' : (this.grantedIn(m) ? 'text-primary-600' : 'text-gray-400'); },
    }"
    x-init="init()"
    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
>
    {{-- Global bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-3 dark:border-white/10">
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            {{ __('security::filament/resources/role.matrix.title') }}
            <span class="ml-2 rounded-md bg-primary-50 px-2 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                <span x-text="grantedTotal()"></span> / <span x-text="total()"></span>
            </span>
        </div>

        <div class="flex items-center gap-2" x-show="! disabled">
            <span class="text-xs font-semibold text-gray-500">{{ __('security::filament/resources/role.matrix.all-modules') }}</span>
            <button type="button" x-on:click="selectAllGlobal()" class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-500">
                {{ __('security::filament/resources/role.matrix.select-all') }}
            </button>
            <button type="button" x-on:click="deselectAllGlobal()" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">
                {{ __('security::filament/resources/role.matrix.deselect-all') }}
            </button>
        </div>
    </div>

    <div class="flex">
        {{-- Module navigator --}}
        <div class="w-60 shrink-0 border-r border-gray-200 p-3 dark:border-white/10">
            <input
                type="text"
                x-model="q"
                placeholder="{{ __('security::filament/resources/role.matrix.search') }}"
                class="p-2 mb-3 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5"
            >
            <p class="px-1 pb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                {{ __('security::filament/resources/role.matrix.modules') }}
            </p>
            <div class="space-y-0.5 overflow-y-auto">
                <template x-for="m in filteredModules()" :key="m.key">
                    <button
                        type="button"
                        x-on:click="active = m.key"
                        :class="active === m.key ? 'bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5'"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm"
                    >
                        <span class="font-medium" x-text="m.label"></span>
                        <span class="text-xs tabular-nums" :class="countClass(m)" x-text="grantedIn(m) + '/' + m.names.length"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Active module panel --}}
        <div class="min-w-0 flex-1 p-4">
            <template x-if="current">
                <div>
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="current.label"></h3>
                            <p class="text-xs text-gray-500">
                                <span x-text="grantedIn(current)"></span> / <span x-text="current.names.length"></span>
                                {{ __('security::filament/resources/role.matrix.granted') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2" x-show="! disabled">
                            <button type="button" x-on:click="setMany(current.names, true, 'manual')" class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-500">
                                {{ __('security::filament/resources/role.matrix.select-all') }}
                            </button>
                            <button type="button" x-on:click="setMany(current.names, false, 'manual')" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">
                                {{ __('security::filament/resources/role.matrix.deselect-all') }}
                            </button>
                        </div>
                    </div>

                    {{-- Resources matrix --}}
                    <template x-if="current.resources.length">
                        <div class="overflow-x-auto rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 ">
                                            {{ __('security::filament/resources/role.matrix.model') }}
                                        </th>
                                        <template x-for="col in current.columns" :key="col">
                                            <th class="px-2 py-2 text-center ">
                                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200" x-text="abilityLabel(col)"></div>
                                                <div class="mt-0.5 text-[10px]" x-show="! disabled">
                                                    <button type="button" class="text-primary-600 hover:text-primary-500" x-on:click="colSet(current, col, true)"><x-filament::icon icon="heroicon-o-check-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.all') }}</span></button>
                                                    <span class="text-gray-300">·</span>
                                                    <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="colSet(current, col, false)"><x-filament::icon icon="heroicon-o-x-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.none') }}</span></button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="px-2 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500" x-show="! disabled">
                                            {{ __('security::filament/resources/role.matrix.row') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    <template x-for="r in current.resources" :key="r.label">
                                        <tr>
                                            <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200" x-text="r.label"></td>
                                            <template x-for="col in current.columns" :key="col">
                                                <td class="px-2 py-2 text-center ">
                                                    <template x-if="r.abilities[col]">
                                                        <input
                                                            type="checkbox"
                                                            :checked="isOn(r.abilities[col])"
                                                            :disabled="disabled"
                                                            x-on:change="toggle(r.abilities[col])"
                                                            class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-white/20 dark:bg-white/5"
                                                        >
                                                    </template>
                                                    <template x-if="! r.abilities[col]">
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                    </template>
                                                </td>
                                            </template>
                                            <td class="px-2 py-2 text-center text-[11px] " x-show="! disabled">
                                                <button type="button" class="text-primary-600 hover:text-primary-500" x-on:click="setMany(Object.values(r.abilities), true, 'manual')"><x-filament::icon icon="heroicon-o-check-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.all') }}</span></button>
                                                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="setMany(Object.values(r.abilities), false, 'manual')"><x-filament::icon icon="heroicon-o-x-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.none') }}</span></button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    {{-- Pages --}}
                    <template x-if="Object.keys(current.pages).length">
                        <div class="mt-5">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('security::filament/resources/role.matrix.pages') }}</h4>
                                <div class="text-[11px]" x-show="! disabled">
                                    <button type="button" class="text-primary-600 hover:text-primary-500" x-on:click="setMany(Object.keys(current.pages), true, 'manual')"><x-filament::icon icon="heroicon-o-check-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.all') }}</span></button>
                                    <span class="text-gray-300">·</span>
                                    <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="setMany(Object.keys(current.pages), false, 'manual')"><x-filament::icon icon="heroicon-o-x-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.none') }}</span></button>
                                </div>
                            </div>
                            <div class="grid gap-2 grid-cols-[repeat(auto-fit,minmax(220px,1fr))]">
                                <template x-for="entry in Object.entries(current.pages)" :key="entry[0]">
                                    <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <input type="checkbox" :checked="isOn(entry[0])" :disabled="disabled" x-on:change="toggle(entry[0])" class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-white/20 dark:bg-white/5">
                                        <span class="text-sm text-gray-700 dark:text-gray-300" x-text="entry[1]"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Widgets --}}
                    <template x-if="Object.keys(current.widgets).length">
                        <div class="mt-5">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('security::filament/resources/role.matrix.widgets') }}</h4>
                                <div class="text-[11px]" x-show="! disabled">
                                    <button type="button" class="text-primary-600 hover:text-primary-500" x-on:click="setMany(Object.keys(current.widgets), true, 'manual')"><x-filament::icon icon="heroicon-o-check-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.all') }}</span></button>
                                    <span class="text-gray-300">·</span>
                                    <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="setMany(Object.keys(current.widgets), false, 'manual')"><x-filament::icon icon="heroicon-o-x-circle" class="inline-block size-5" /><span class="sr-only">{{ __('security::filament/resources/role.matrix.none') }}</span></button>
                                </div>
                            </div>
                            <div class="grid gap-2 grid-cols-[repeat(auto-fit,minmax(220px,1fr))]">
                                <template x-for="entry in Object.entries(current.widgets)" :key="entry[0]">
                                    <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <input type="checkbox" :checked="isOn(entry[0])" :disabled="disabled" x-on:change="toggle(entry[0])" class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-white/20 dark:bg-white/5">
                                        <span class="text-sm text-gray-700 dark:text-gray-300" x-text="entry[1]"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="! current">
                <p class="py-10 text-center text-sm text-gray-400">{{ __('security::filament/resources/role.matrix.empty') }}</p>
            </template>
        </div>
    </div>
</div>
