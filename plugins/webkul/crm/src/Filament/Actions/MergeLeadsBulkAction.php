<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Text;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\LeadMerger;

class MergeLeadsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'mergeLeads';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/similar-leads.merge.label'))
            ->icon('heroicon-o-arrows-pointing-in')
            ->color('gray')
            ->modalHeading(__('crm::filament/similar-leads.merge.heading'))
            ->modalSubmitActionLabel(__('crm::filament/similar-leads.merge.submit'))
            ->deselectRecordsAfterCompletion()
            ->fillForm(fn (EloquentCollection $records): array => [
                'head' => $records->count() > 1 ? app(LeadMerger::class)->pickHead(static::fresh($records))->getKey() : null,
            ])
            ->schema(fn (EloquentCollection $records): array => [
                Text::make(__('crm::filament/similar-leads.merge.description')),
                Select::make('head')
                    ->label(__('crm::filament/similar-leads.merge.fields.head'))
                    ->helperText(__('crm::filament/similar-leads.merge.fields.head-helper'))
                    ->options(MergeLeadsAction::recordOptions(static::fresh($records)))
                    ->required(),
                ...MergeLeadsAction::assignmentFields(fn (): Collection => static::fresh($records)),
            ])
            ->action(function (EloquentCollection $records, array $data, BulkAction $action): void {
                $records = static::fresh($records);

                MergeLeadsAction::mergeSelected($records, $records->modelKeys(), $data['head'] ?? null, $action, $data);
            });
    }

    protected static function fresh(EloquentCollection $records): Collection
    {
        return Lead::query()->withTrashed()->with(['stage', 'user', 'tags'])->whereKey($records->modelKeys())->get();
    }
}
