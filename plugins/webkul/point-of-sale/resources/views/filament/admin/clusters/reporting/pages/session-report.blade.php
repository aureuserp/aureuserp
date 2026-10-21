<x-filament-panels::page>
    @php($report = $this->getReport())

    <x-filament::section>
        <x-slot name="heading">
            {{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.filters.heading') }}
        </x-slot>

        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="sessionId">
                <option value="">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.filters.choose') }}</option>

                @foreach ($this->getSessions() as $session)
                    <option value="{{ $session->id }}">{{ $session->name }} — {{ $session->config?->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </x-filament::section>

    @if ($report)
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.orders.heading') }}
                </x-slot>

                <dl class="flex flex-col gap-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.orders.count') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ $report['orders']['count'] }}</dd>
                    </div>

                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.orders.refunds') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ $report['orders']['refunds'] }}</dd>
                    </div>

                    <div class="flex justify-between gap-3 font-semibold">
                        <dt class="text-gray-950 dark:text-white">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.orders.total') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['orders']['total'], 2) }}</dd>
                    </div>
                </dl>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    {{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.payments.heading') }}
                </x-slot>

                <div class="flex flex-col gap-2 text-sm">
                    @forelse ($report['payments'] as $payment)
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-950 dark:text-white">{{ $payment['name'] }}</span>
                            <span class="tabular-nums text-gray-950 dark:text-white">{{ number_format($payment['amount'], 2) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.payments.empty') }}</p>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    {{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.cash.heading') }}
                </x-slot>

                <dl class="flex flex-col gap-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.cash.opening') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['cash']['opening'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.cash.movements') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['cash']['movements'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.cash.expected') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['cash']['expected'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between gap-3 font-semibold">
                        <dt class="text-gray-950 dark:text-white">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.cash.difference') }}</dt>
                        <dd class="tabular-nums text-gray-950 dark:text-white">
                            {{ $report['cash']['difference'] === null ? '—' : number_format($report['cash']['difference'], 2) }}
                        </dd>
                    </div>
                </dl>
            </x-filament::section>
        </div>
    @else
        <x-filament::section>
            <x-filament::empty-state
                icon="heroicon-o-clipboard-document-list"
                :heading="__('point-of-sale::filament/admin/clusters/reporting/pages/session-report.empty.heading')"
                :description="__('point-of-sale::filament/admin/clusters/reporting/pages/session-report.empty.description')"
            />
        </x-filament::section>
    @endif
</x-filament-panels::page>
