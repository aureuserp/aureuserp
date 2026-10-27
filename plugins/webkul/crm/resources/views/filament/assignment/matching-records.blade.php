@php
    use Webkul\Crm\Filament\Support\LeadResourceResolver;
@endphp

<div class="space-y-3">
    @if ($records->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('crm::filament/assignment.rules.records-modal.empty') }}</p>
    @else
        <div class="overflow-x-auto rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
            <table class="w-full text-start text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">
                    <tr>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.name') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.type') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.contact') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.email') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.salesperson') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('crm::filament/assignment.rules.records-modal.columns.expected-revenue') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('crm::filament/assignment.rules.records-modal.columns.stage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($records as $record)
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-950 dark:text-white">
                                <a href="{{ LeadResourceResolver::viewUrl($record) }}" target="_blank" class="hover:underline">{{ $record->name }}</a>
                            </td>
                            <td class="whitespace-nowrap px-3 py-2">
                                <x-filament::badge size="sm" :color="$record->isLead() ? 'gray' : 'primary'">{{ $record->type?->getLabel() }}</x-filament::badge>
                            </td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $record->contact_name ?: $record->partner?->name ?: '—' }}</td>
                            <td class="max-w-48 truncate px-3 py-2 text-gray-700 dark:text-gray-300">{{ $record->email ?: '—' }}</td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $record->user?->name ?: '—' }}</td>
                            <td class="px-3 py-2 text-end text-gray-700 dark:text-gray-300">{{ money((float) $record->expected_revenue, $record->company?->currency?->code ?? default_currency_code()) }}</td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $record->stage?->name ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($total > $records->count())
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('crm::filament/assignment.rules.records-modal.showing', ['shown' => $records->count(), 'total' => $total]) }}</p>
        @endif
    @endif
</div>
