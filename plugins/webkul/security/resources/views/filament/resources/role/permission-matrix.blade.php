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
        _lock: false,
        init() {
            (this.cfg.initial || []).forEach((n) => (this.selected[n] = true));
            this.active = this.cfg.modules.length ? this.cfg.modules[0].key : null;
            this.$wire.$watch('data.select_all_permissions', (v) => { if (this._lock) return; v ? this.selectAllGlobal() : this.deselectAllGlobal(); });
            this.syncToggle();
        },
        syncToggle() {
            const all = this.total() > 0 && this.grantedTotal() === this.total();
            this._lock = true;
            this.$wire.$set('data.select_all_permissions', all, false);
            this.$nextTick(() => { this._lock = false; });
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
        toggle(n) { this.selected[n] = ! this.selected[n]; this.setMode('manual'); this.pushState(); this.syncToggle(); },
        setMany(names, val, mode) { names.forEach((n) => (this.selected[n] = val)); if (mode) this.setMode(mode); this.pushState(); this.syncToggle(); },
        colSet(m, col, val) { this.setMany(this.colNames(m, col), val, 'manual'); },
        selectAllGlobal() { this.cfg.allNames.forEach((n) => (this.selected[n] = true)); this.setMode('all'); this.pushState(); },
        deselectAllGlobal() { this.cfg.allNames.forEach((n) => (this.selected[n] = false)); this.setMode('none'); this.pushState(); },
        moduleAll(m) { return !! m && m.names.length > 0 && this.grantedIn(m) === m.names.length; },
        toggleModule(m) { if (m) this.setMany(m.names, ! this.moduleAll(m), 'manual'); },
        allOn(names) { return names.length > 0 && names.every((n) => this.isOn(n)); },
        toggleNames(names) { this.setMany(names, ! this.allOn(names), 'manual'); },
        grantedIn(m) { return m.names.filter((n) => this.isOn(n)).length; },
        grantedTotal() { return this.cfg.allNames.filter((n) => this.isOn(n)).length; },
        total() { return this.cfg.allNames.length; },
        countClass(m) { return this.grantedIn(m) === m.names.length && m.names.length ? 'fi-color-success' : (this.grantedIn(m) ? 'fi-color-primary' : 'fi-color-gray'); },
    }"
    x-init="init()"
>
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center gap-2">
                {{ __('security::filament/resources/role.matrix.title') }}
                <x-filament::badge color="primary">
                    <span x-text="grantedTotal()"></span> / <span x-text="total()"></span>
                </x-filament::badge>
            </span>
        </x-slot>

        <div class="flex flex-col gap-4 sm:flex-row!">
            <div class="shrink-0 border-b border-gray-200 pb-4 sm:w-60 sm:border-b-0 sm:border-r sm:pb-0 sm:pr-4 dark:border-white/10">
                <x-filament::input.wrapper class="mb-3">
                    <x-filament::input
                        type="text"
                        x-model="q"
                        :placeholder="__('security::filament/resources/role.matrix.search')"
                    />
                </x-filament::input.wrapper>

                <p class="px-1 pb-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    {{ __('security::filament/resources/role.matrix.all-modules') }}
                </p>
                <div class="max-h-60 space-y-0.5 overflow-y-auto sm:max-h-none">
                    <template x-for="m in filteredModules()" :key="m.key">
                        <button
                            type="button"
                            x-on:click="active = m.key"
                            :class="active === m.key ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-white/5'"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm"
                        >
                            <span class="font-medium" x-text="m.label"></span>
                            <span class="text-xs tabular-nums" :class="countClass(m)" x-text="grantedIn(m) + '/' + m.names.length"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="min-w-0 flex-1">
                <template x-if="current">
                    <div>
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-gray-950 dark:text-white" x-text="current.label"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="grantedIn(current)"></span> / <span x-text="current.names.length"></span>
                                    {{ __('security::filament/resources/role.matrix.granted') }}
                                </p>
                            </div>
                            <label class="flex items-center gap-2" x-show="! disabled">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('security::filament/resources/role.matrix.select-all') }}
                                </span>
                                @include('security::filament.resources.role.partials.matrix-toggle', ['on' => 'moduleAll(current)', 'click' => 'toggleModule(current)'])
                            </label>
                        </div>

                        <template x-if="current.resources.length">
                            <div class="overflow-x-auto rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                                <table class="min-w-full fi-ta-table">
                                    <thead>
                                        <tr>
                                            <th class="fi-ta-header-cell fi-ta-header-cell-name">
                                                {{ __('security::filament/resources/role.matrix.model') }}
                                            </th>
                                            <template x-for="col in current.columns" :key="col">
                                                <th class="whitespace-nowrap px-2 py-2 text-center fi-ta-header-cell fi-ta-header-cell-name">
                                                    <div x-text="abilityLabel(col)"></div>
                                                    <div class="mt-1 flex items-center justify-center" x-show="! disabled">
                                                        @include('security::filament.resources.role.partials.matrix-toggle', ['on' => 'allOn(colNames(current, col))', 'click' => 'toggleNames(colNames(current, col))', 'sizeClass' => 'scale-75'])
                                                    </div>
                                                </th>
                                            </template>
                                            <th class=" text-center fi-ta-header-cell fi-ta-header-cell-name" x-show="! disabled">
                                                {{ __('security::filament/resources/role.matrix.action') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                        <template x-for="r in current.resources" :key="r.label">
                                            <tr>
                                                <td class="whitespace-nowrap fi-ta-cell fi-ta-cell-guard-name" x-text="r.label"></td>
                                                <template x-for="col in current.columns" :key="col">
                                                    <td class="px-2 py-2 text-center fi-ta-cell fi-ta-cell-guard-name">
                                                        <template x-if="r.abilities[col]">
                                                            <div class="flex justify-center">
                                                                <x-filament::input.checkbox
                                                                    x-bind:checked="isOn(r.abilities[col])"
                                                                    x-bind:disabled="disabled"
                                                                    x-on:change="toggle(r.abilities[col])"
                                                                />
                                                            </div>
                                                        </template>
                                                        <template x-if="! r.abilities[col]">
                                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                                        </template>
                                                    </td>
                                                </template>
                                                <td class="px-2 py-2 text-center" x-show="! disabled">
                                                    <div class="flex items-center justify-center">
                                                        @include('security::filament.resources.role.partials.matrix-toggle', ['on' => 'allOn(Object.values(r.abilities))', 'click' => 'toggleNames(Object.values(r.abilities))', 'sizeClass' => 'scale-75'])
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <template x-if="Object.keys(current.pages).length">
                            <div class="mt-5">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('security::filament/resources/role.matrix.pages') }}</h4>
                                    <div class="flex items-center" x-show="! disabled">
                                        @include('security::filament.resources.role.partials.matrix-toggle', ['on' => 'allOn(Object.keys(current.pages))', 'click' => 'toggleNames(Object.keys(current.pages))'])
                                    </div>
                                </div>
                                <div class="grid gap-2 grid-cols-[repeat(auto-fit,minmax(220px,1fr))]">
                                    <template x-for="entry in Object.entries(current.pages)" :key="entry[0]">
                                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-white/5">
                                            <x-filament::input.checkbox x-bind:checked="isOn(entry[0])" x-bind:disabled="disabled" x-on:change="toggle(entry[0])" />
                                            <span class="text-sm text-gray-700 dark:text-gray-300" x-text="entry[1]"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="Object.keys(current.widgets).length">
                            <div class="mt-5">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('security::filament/resources/role.matrix.widgets') }}</h4>
                                    <div class="flex items-center" x-show="! disabled">
                                        @include('security::filament.resources.role.partials.matrix-toggle', ['on' => 'allOn(Object.keys(current.widgets))', 'click' => 'toggleNames(Object.keys(current.widgets))'])
                                    </div>
                                </div>
                                <div class="grid gap-2 grid-cols-[repeat(auto-fit,minmax(220px,1fr))]">
                                    <template x-for="entry in Object.entries(current.widgets)" :key="entry[0]">
                                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-white/5">
                                            <x-filament::input.checkbox x-bind:checked="isOn(entry[0])" x-bind:disabled="disabled" x-on:change="toggle(entry[0])" />
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
    </x-filament::section>
</div>
