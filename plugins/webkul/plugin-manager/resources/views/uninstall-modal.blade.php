<div class="space-y-4">
    {{-- Uninstall Confirmation --}}
    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
            Uninstall Confirmation
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to uninstall the <strong>{{ $record->name }}</strong> plugin?
        </p>
        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
            ⚠️ This action cannot be undone and will permanently delete data.
        </div>
    </div>

    {{-- Dependent Plugins --}}
    @if(count($dependents) > 0)
    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
            Dependent Plugins
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            These plugins depend on this one and will also be uninstalled
        </p>
        <div class="mt-3 space-y-2">
            @foreach($dependents as $dependent)
                <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ ucfirst($dependent) }}
                    </span>
                    @if(\Webkul\Support\Package::isPluginInstalled($dependent))
                        <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                            Installed
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                            Not Installed
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Data Impact --}}
    @if(count($tables) > 0)
    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
            Data Impact
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            The following database tables contain data that will be permanently deleted
        </p>
        <div class="mt-3 max-h-50 overflow-y-auto scrollbar-thin-transparent space-y-2">
            @foreach($tables as $tableData)
                <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $tableData['table'] }}
                        </span>
                    </div>
                    <span class="inline-flex items-center rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                        {{ number_format($tableData['count']) }} records
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>