<div class="space-y-4">
    {{-- Uninstall Confirmation --}}
    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
            {{ __('plugin-manager::views/uninstall-modal.uninstall.title') }}
        </h3>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {!! __('plugin-manager::views/uninstall-modal.uninstall.message', ['name' => '<strong>' . e($record->name) . '</strong>']) !!}
        </p>

        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ __('plugin-manager::views/uninstall-modal.uninstall.warning') }}
        </div>
    </div>

    {{-- Dependent Plugins --}}
    @if(count($dependents) > 0)
        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                {{ __('plugin-manager::views/uninstall-modal.dependents.title') }}
            </h3>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('plugin-manager::views/uninstall-modal.dependents.description') }}
            </p>

            <div class="mt-3 space-y-2">
                @foreach($dependents as $dependent)
                    <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ ucfirst($dependent) }}
                        </span>

                        @if(\Webkul\Support\Package::isPluginInstalled($dependent))
                            <span
                                class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                {{ __('plugin-manager::views/uninstall-modal.dependents.installed') }}
                            </span>
                        @else
                            <span
                                class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                {{ __('plugin-manager::views/uninstall-modal.dependents.not_installed') }}
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
                {{ __('plugin-manager::views/uninstall-modal.data_impact.title') }}
            </h3>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('plugin-manager::views/uninstall-modal.data_impact.description') }}
            </p>

            <div class="mt-3 max-h-50 overflow-y-auto scrollbar-thin-transparent space-y-2">
                @foreach($tables as $tableData)
                    <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $tableData['table'] }}
                            </span>
                        </div>

                        <span
                            class="inline-flex items-center rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                            {{ __('plugin-manager::views/uninstall-modal.data_impact.records', ['count' => number_format($tableData['count'])]) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
