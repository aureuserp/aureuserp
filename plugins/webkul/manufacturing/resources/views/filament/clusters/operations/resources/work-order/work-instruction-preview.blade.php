@php
    use Webkul\Manufacturing\Enums\OperationWorksheetType;
    use Webkul\Manufacturing\Models\Operation;

    $record = $getRecord();
    $operation = $record?->operation;

    if (! $operation && isset($get)) {
        $operationId = $get('operation_id');

        if (filled($operationId)) {
            $operation = Operation::query()
                ->withTrashed()
                ->find($operationId);
        }
    }

    if (! $operation && filled($record?->operation_id)) {
        $operation = Operation::query()
            ->withTrashed()
            ->find($record->operation_id);
    }

    $worksheetType = $operation?->worksheet_type;

    if (is_string($worksheetType)) {
        $worksheetType = OperationWorksheetType::tryFrom($worksheetType);
    }

    $pdfUrl = filled($operation?->worksheet) ? \Illuminate\Support\Facades\Storage::disk('public')->url($operation->worksheet) : null;
    $externalUrl = filled($operation?->worksheet_google_slide_url) ? $operation->worksheet_google_slide_url : null;
    $previewUrl = $externalUrl;

    if (filled($externalUrl) && str_contains($externalUrl, 'docs.google.com')) {
        $previewUrl = preg_replace('/\/edit(?:\?.*)?(?:#.*)?$/', '/preview', $externalUrl) ?? $externalUrl;
    }
@endphp

<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-3">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    {{ __('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.work-instruction.entries.operation') }}
                </p>

                <p class="text-base font-semibold text-gray-900">
                    {{ $operation?->name ?? '—' }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-sm font-medium text-gray-500">
                    {{ __('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.work-instruction.entries.worksheet') }}
                </p>

                <p class="text-sm font-semibold text-gray-900">
                    {{ $worksheetType?->getLabel() ?? '—' }}
                </p>
            </div>
        </div>

        <div class="pt-4">
            @if ($worksheetType === OperationWorksheetType::TEXT)
                <div class="min-h-48 whitespace-pre-wrap rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                    {{ filled($operation?->note) ? $operation->note : '—' }}
                </div>
            @elseif ($worksheetType === OperationWorksheetType::PDF && filled($pdfUrl))
                <div class="space-y-3">
                    <a
                        href="{{ $pdfUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-500"
                    >
                        {{ basename((string) $operation->worksheet) }}
                    </a>

                    <iframe
                        src="{{ $pdfUrl }}"
                        title="{{ $operation?->name }}"
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50"
                        style="height: 800px; min-height: 800px;"
                    ></iframe>
                </div>
            @elseif ($worksheetType === OperationWorksheetType::GOOGLE_SLIDE && filled($previewUrl))
                <div class="space-y-3">
                    <a
                        href="{{ $externalUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center break-all text-sm font-medium text-primary-600 hover:text-primary-500"
                    >
                        {{ $externalUrl }}
                    </a>

                    <iframe
                        src="{{ $previewUrl }}"
                        title="{{ $operation?->name }}"
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50"
                        style="height: 800px; min-height: 800px;"
                        allowfullscreen
                    ></iframe>
                </div>
            @else
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-sm text-gray-500">
                    —
                </div>
            @endif
        </div>
    </div>
</div>
