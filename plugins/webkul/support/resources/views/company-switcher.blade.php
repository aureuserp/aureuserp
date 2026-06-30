<div>
    @if ($companies->count() > 1)
        <x-filament::dropdown placement="bottom-end" width="xs" teleport>
            <x-slot name="trigger">
                <button
                    type="button"
                    style="display:flex;align-items:center;gap:0.375rem;border-radius:0.5rem;padding:0.375rem 0.625rem;font-size:0.875rem;font-weight:500;"
                >
                    <x-filament::icon icon="heroicon-o-building-office-2" style="height:1.25rem;width:1.25rem;" />

                    <span>
                        @if (count($active) === 1)
                            {{ $companies->firstWhere('id', $active[0])?->name }}
                        @else
                            {{ count($active) }} {{ __('support::company-switcher.companies') }}
                        @endif
                    </span>

                    <x-filament::icon icon="heroicon-m-chevron-down" style="height:1rem;width:1rem;" />
                </button>
            </x-slot>

            <form method="POST" action="{{ route('company-context.set') }}">
                @csrf

                <div style="display:flex;flex-direction:column;gap:0.125rem;padding:0.5rem;max-height:18rem;overflow-y:auto;">
                    @foreach ($companies as $company)
                        <label style="display:flex;align-items:center;gap:0.625rem;padding:0.5rem 0.625rem;border-radius:0.5rem;font-size:0.875rem;cursor:pointer;">
                            <x-filament::input.checkbox
                                name="companies[]"
                                value="{{ $company->id }}"
                                :checked="in_array($company->id, $active)"
                            />

                            <span>{{ $company->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;padding:0.5rem;border-top:1px solid rgba(128,128,128,0.2);">
                    <x-filament::button type="submit" size="sm" style="width:100%;justify-content:center;">
                        {{ __('support::company-switcher.confirm') }}
                    </x-filament::button>

                    <x-filament::button type="reset" color="gray" size="sm" style="width:100%;justify-content:center;">
                        {{ __('support::company-switcher.reset') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::dropdown>
    @endif
</div>
