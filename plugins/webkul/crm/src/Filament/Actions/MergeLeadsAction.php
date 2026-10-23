<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\LeadMerger;

class MergeLeadsAction extends Action
{
    protected ?\Closure $candidates = null;

    public static function getDefaultName(): ?string
    {
        return 'mergeLeads';
    }

    public function candidates(\Closure $candidates): static
    {
        $this->candidates = $candidates;

        return $this;
    }

    public function getMergeCandidates(): Collection
    {
        return $this->candidates ? ($this->candidates)() : collect();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/similar-leads.merge.label'))
            ->icon('heroicon-o-arrows-pointing-in')
            ->color('primary')
            ->modalHeading(__('crm::filament/similar-leads.merge.heading'))
            ->modalSubmitActionLabel(__('crm::filament/similar-leads.merge.submit'))
            ->modalWidth('2xl')
            ->visible(fn (): bool => $this->getMergeCandidates()->count() > 1)
            ->fillForm(fn (): array => [
                'records' => $this->getMergeCandidates()->modelKeys(),
                'head'    => app(LeadMerger::class)->pickHead($this->getMergeCandidates())->getKey(),
            ])
            ->schema(fn (): array => [
                Text::make(__('crm::filament/similar-leads.merge.description')),
                CheckboxList::make('records')
                    ->label(__('crm::filament/similar-leads.merge.fields.records'))
                    ->options(static::recordOptions($this->getMergeCandidates()))
                    ->required()
                    ->minItems(2)
                    ->live(),
                Select::make('head')
                    ->label(__('crm::filament/similar-leads.merge.fields.head'))
                    ->helperText(__('crm::filament/similar-leads.merge.fields.head-helper'))
                    ->options(fn (Get $get): array => array_intersect_key(
                        static::recordOptions($this->getMergeCandidates()),
                        array_flip((array) $get('records')),
                    ))
                    ->required(),
                ...static::assignmentFields(fn (Get $get): Collection => $this->getMergeCandidates()->whereIn('id', array_map('intval', (array) $get('records')))),
            ])
            ->action(function (array $data, Action $action): void {
                static::mergeSelected($this->getMergeCandidates(), (array) ($data['records'] ?? []), $data['head'] ?? null, $action, $data);
            });
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
            ->label(__('crm::filament/similar-leads.merge.fields.'.$label))
            ->helperText(__('crm::filament/similar-leads.merge.fields.conflict-helper'))
            ->options(fn (Get $get): array => static::conflictOptions($records($get), $name, $relation))
            ->visible(fn (Get $get): bool => count(static::conflictOptions($records($get), $name, $relation)) > 1)
            ->required();

        return [
            $field('user_id', 'user', 'salesperson'),
            $field('team_id', 'team', 'team'),
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

        foreach (['user_id' => 'user', 'team_id' => 'team'] as $field => $relation) {
            $options = static::conflictOptions($records, $field, $relation);

            if (count($options) < 2) {
                continue;
            }

            $chosen = (int) ($data[$field] ?? 0);

            if (! array_key_exists($chosen, $options)) {
                Notification::make()->danger()->title(__('crm::filament/similar-leads.merge.notifications.choose-'.$relation))->send();

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
