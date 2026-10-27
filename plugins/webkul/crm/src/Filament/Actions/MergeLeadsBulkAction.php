<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
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
            ->modalWidth('4xl')
            ->fillForm(function (EloquentCollection $records): array {
                $leads = static::fresh($records);

                return [
                    'records' => $leads->map(fn (Lead $lead): array => static::recordRow($lead))->values()->all(),
                    'head'    => $leads->count() > 1 ? app(LeadMerger::class)->pickHead($leads)->getKey() : null,
                ];
            })
            ->schema(function (EloquentCollection $records): array {
                $leads = static::fresh($records);

                $remaining = fn (Get $get): Collection => $leads->whereIn('id', static::rowIds((array) $get('records')))->values();

                return [
                    Text::make(__('crm::filament/similar-leads.merge.description')),
                    Repeater::make('records')
                        ->label(__('crm::filament/similar-leads.merge.fields.records'))
                        ->table([
                            TableColumn::make(__('crm::filament/similar-leads.table.columns.name')),
                            TableColumn::make(__('crm::filament/similar-leads.table.columns.type')),
                            TableColumn::make(__('crm::filament/similar-leads.table.columns.stage')),
                            TableColumn::make(__('crm::filament/similar-leads.table.columns.email')),
                            TableColumn::make(__('crm::filament/similar-leads.table.columns.salesperson')),
                        ])
                        ->schema([
                            Group::make([
                                Hidden::make('id'),
                                Hidden::make('name'),
                                Hidden::make('type'),
                                Hidden::make('stage'),
                                Hidden::make('email'),
                                Hidden::make('salesperson'),
                                Text::make(fn (Get $get): string => (string) $get('name'))->weight('medium'),
                            ]),
                            Text::make(fn (Get $get): string => (string) $get('type')),
                            Text::make(fn (Get $get): string => (string) ($get('stage') ?? '—')),
                            Text::make(fn (Get $get): string => (string) ($get('email') ?? '—')),
                            Text::make(fn (Get $get): string => (string) ($get('salesperson') ?? '—')),
                        ])
                        ->addable(false)
                        ->reorderable(false)
                        ->minItems(2)
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) use ($remaining): void {
                            $records = $remaining($get);

                            if (! $records->contains('id', (int) $get('head'))) {
                                $set('head', $records->count() > 1 ? app(LeadMerger::class)->pickHead($records)->getKey() : null);
                            }
                        }),
                    Select::make('head')
                        ->label(__('crm::filament/similar-leads.merge.fields.head'))
                        ->helperText(__('crm::filament/similar-leads.merge.fields.head-helper'))
                        ->options(fn (Get $get): array => static::recordOptions($remaining($get)))
                        ->required(),
                    ...static::assignmentFields($remaining),
                ];
            })
            ->action(function (EloquentCollection $records, array $data, BulkAction $action): void {
                static::mergeSelected(static::fresh($records), static::rowIds((array) ($data['records'] ?? [])), $data['head'] ?? null, $action, $data);
            });
    }

    protected static function recordRow(Lead $lead): array
    {
        return [
            'id'          => $lead->getKey(),
            'name'        => $lead->name,
            'type'        => $lead->type->getLabel(),
            'stage'       => $lead->trashed() ? __('crm::filament/similar-leads.badges.lost') : $lead->stage?->name,
            'email'       => $lead->email ?: $lead->phone,
            'salesperson' => $lead->user?->name,
        ];
    }

    protected static function rowIds(array $rows): array
    {
        return collect($rows)->pluck('id')->filter()->map(fn (mixed $id): int => (int) $id)->values()->all();
    }

    protected static function fresh(EloquentCollection $records): Collection
    {
        return Lead::query()->withTrashed()->with(['stage', 'user', 'tags'])->whereKey($records->modelKeys())->get();
    }

    public static function recordOptions(Collection $records): array
    {
        return $records->mapWithKeys(fn (Lead $lead): array => [
            $lead->getKey() => collect([
                $lead->name,
                $lead->isLead() ? __('crm::models/lead.title') : __('crm::models/opportunity.title'),
                $lead->stage?->name,
                $lead->email ?: $lead->phone,
                $lead->user?->name,
                $lead->trashed() ? __('crm::filament/similar-leads.badges.lost') : null,
            ])->filter()->implode(' · '),
        ])->all();
    }

    public static function assignmentFields(\Closure $records): array
    {
        $field = fn (string $name, string $relation, string $label): Select => Select::make($name)
            ->label($label)
            ->helperText(__('crm::filament/similar-leads.merge.fields.conflict-helper'))
            ->options(fn (Get $get): array => static::conflictOptions($records($get), $name, $relation))
            ->visible(fn (Get $get): bool => count(static::conflictOptions($records($get), $name, $relation)) > 1)
            ->required();

        return [
            $field('user_id', 'user', __('crm::filament/similar-leads.merge.fields.salesperson')),
            $field('team_id', 'team', __('crm::filament/similar-leads.merge.fields.team')),
        ];
    }

    public static function conflictOptions(Collection $records, string $field, string $relation): array
    {
        return $records
            ->filter(fn (Lead $lead): bool => filled($lead->getAttribute($field)))
            ->mapWithKeys(fn (Lead $lead): array => [(int) $lead->getAttribute($field) => (string) ($lead->{$relation}?->name ?? '#'.$lead->getAttribute($field))])
            ->all();
    }

    public static function mergeSelected(Collection $candidates, array $selectedIds, mixed $headId, Action $action, array $data = []): void
    {
        $records = $candidates->whereIn('id', array_map('intval', $selectedIds))->values();

        $head = $records->firstWhere('id', (int) $headId);

        if ($records->count() < 2 || ! $head) {
            Notification::make()->danger()->title(__('crm::filament/similar-leads.merge.notifications.invalid'))->send();

            $action->halt();
        }

        $others = $records->reject(fn (Lead $lead): bool => $lead->is($head));

        if (! static::canMerge($head, $others)) {
            Notification::make()->danger()->title(__('crm::filament/similar-leads.merge.notifications.forbidden'))->send();

            $action->halt();
        }

        if ($records->pluck('company_id')->unique()->count() > 1) {
            Notification::make()->danger()->title(__('crm::filament/similar-leads.merge.notifications.companies'))->send();

            $action->halt();
        }

        $assignments = [];

        foreach ([
            'user_id' => ['user', __('crm::filament/similar-leads.merge.notifications.choose-user')],
            'team_id' => ['team', __('crm::filament/similar-leads.merge.notifications.choose-team')],
        ] as $field => [$relation, $message]) {
            $options = static::conflictOptions($records, $field, $relation);

            if (count($options) < 2) {
                continue;
            }

            $chosen = (int) ($data[$field] ?? 0);

            if (! array_key_exists($chosen, $options)) {
                Notification::make()->danger()->title($message)->send();

                $action->halt();
            }

            $assignments[$field] = $chosen;
        }

        $merged = app(LeadMerger::class)->merge($records, $head, $assignments);

        Notification::make()
            ->success()
            ->title(__('crm::filament/similar-leads.merge.notifications.merged.title'))
            ->body(__('crm::filament/similar-leads.merge.notifications.merged.body', ['count' => $others->count(), 'name' => $merged->name]))
            ->send();

        $action->redirect(LeadResourceResolver::viewUrl($merged));
    }

    public static function canMerge(Lead $head, Collection $others): bool
    {
        $headResource = LeadResourceResolver::resourceFor($head);

        return $headResource::canEdit($head)
            && (! $head->trashed() || $headResource::canRestore($head))
            && $others->every(fn (Lead $lead): bool => LeadResourceResolver::resourceFor($lead)::canForceDelete($lead));
    }
}
