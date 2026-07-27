<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry as Registry;
use Webkul\Partner\Models\Partner;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(array_merge([
                Stack::make([
                    ImageColumn::make('avatar')
                        ->height(200)
                        ->width(250)
                        ->alignCenter(),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('parent.name')
                                ->label(__('partners::filament/resources/partner.table.columns.parent'))
                                ->icon(fn (Partner $record) => $record->parent->account_type === AccountType::INDIVIDUAL->value ? 'heroicon-o-user' : 'heroicon-o-building-office')
                                ->tooltip(__('partners::filament/resources/partner.table.columns.parent'))
                                ->sortable(),
                        ])
                            ->visible(fn (Partner $record) => filled($record->parent)),
                        Stack::make([
                            TextColumn::make('job_title')
                                ->icon('heroicon-m-briefcase')
                                ->searchable()
                                ->sortable()
                                ->label(__('partners::filament/resources/partner.table.groups.job-title')),
                        ])
                            ->visible(fn ($record) => filled($record->job_title)),
                        Stack::make([
                            TextColumn::make('email')
                                ->icon('heroicon-o-envelope')
                                ->searchable()
                                ->sortable()
                                ->label(__('partners::filament/resources/partner.table.columns.work-email'))
                                ->color('gray')
                                ->limit(20),
                        ])
                            ->visible(fn ($record) => filled($record->email)),
                        Stack::make([
                            TextColumn::make('phone')
                                ->icon('heroicon-o-phone')
                                ->searchable()
                                ->label(__('partners::filament/resources/partner.table.columns.work-phone'))
                                ->color('gray')
                                ->limit(30)
                                ->sortable(),
                        ])
                            ->visible(fn ($record) => filled($record->phone)),
                        Stack::make([
                            TextColumn::make('tags.name')
                                ->badge()
                                ->state(function (Partner $record): array {
                                    return $record->tags()->get()->map(fn ($tag) => [
                                        'label' => $tag->name,
                                        'color' => $tag->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                ->weight(FontWeight::Bold),
                        ])
                            ->visible(fn ($record): bool => (bool) $record->tags?->count()),
                    ])->space(1),
                ])->space(4),
            ], Registry::renderTable('columns')))
            ->groups(array_merge([
                Tables\Grouping\Group::make('account_type')
                    ->label(__('partners::filament/resources/partner.table.groups.account-type')),
                Tables\Grouping\Group::make('parent.name')
                    ->label(__('partners::filament/resources/partner.table.groups.parent')),
                Tables\Grouping\Group::make('title.name')
                    ->label(__('partners::filament/resources/partner.table.groups.title')),
                Tables\Grouping\Group::make('job_title')
                    ->label(__('partners::filament/resources/partner.table.groups.job-title')),
                Tables\Grouping\Group::make('industry.name')
                    ->label(__('partners::filament/resources/partner.table.groups.industry')),
            ], Registry::renderTable('groups')))
            ->defaultSort('created_at', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(static::constraints()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions(array_merge([
                ActivityTableAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (ForceDeleteAction $action, Partner $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('partners::filament/resources/partner.table.actions.force-delete.notification.error.title'))
                                ->body(__('partners::filament/resources/partner.table.actions.force-delete.notification.error.body'))
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.force-delete.notification.success.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.force-delete.notification.success.body')),
                    ),
            ], Registry::renderTable('actions')))
            ->toolbarActions([
                BulkActionGroup::make(array_merge([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ], Registry::renderTable('bulkActions'))),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['tags'])->where('account_type', '!=', AccountType::ADDRESS);
            })
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->paginated([
                16,
                32,
                64,
                'all',
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    public static function constraints(): array
    {
        $constraints = [
            'account_type' => SelectConstraint::make('account_type')
                ->label(__('partners::filament/resources/partner.table.filters.account-type'))
                ->multiple()
                ->options(AccountType::class)
                ->icon('heroicon-o-bars-2'),
            'name' => TextConstraint::make('name')
                ->label(__('partners::filament/resources/partner.table.filters.name')),
            'email' => TextConstraint::make('email')
                ->label(__('partners::filament/resources/partner.table.filters.email'))
                ->icon('heroicon-o-envelope'),
            'job_title' => TextConstraint::make('job_title')
                ->label(__('partners::filament/resources/partner.table.filters.job-title')),
            'website' => TextConstraint::make('website')
                ->label(__('partners::filament/resources/partner.table.filters.website'))
                ->icon('heroicon-o-globe-alt'),
            'tax_id' => TextConstraint::make('tax_id')
                ->label(__('partners::filament/resources/partner.table.filters.tax-id'))
                ->icon('heroicon-o-identification'),
            'phone' => TextConstraint::make('phone')
                ->label(__('partners::filament/resources/partner.table.filters.phone'))
                ->icon('heroicon-o-phone'),
            'mobile' => TextConstraint::make('mobile')
                ->label(__('partners::filament/resources/partner.table.filters.mobile'))
                ->icon('heroicon-o-phone'),
            'company_registry' => TextConstraint::make('company_registry')
                ->label(__('partners::filament/resources/partner.table.filters.company-registry'))
                ->icon('heroicon-o-clipboard'),
            'reference' => TextConstraint::make('reference')
                ->label(__('partners::filament/resources/partner.table.filters.reference'))
                ->icon('heroicon-o-hashtag'),
            'parent' => RelationshipConstraint::make('parent')
                ->label(__('partners::filament/resources/partner.table.filters.parent'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                )
                ->icon('heroicon-o-user'),
            'creator' => RelationshipConstraint::make('creator')
                ->label(__('partners::filament/resources/partner.table.filters.creator'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                )
                ->icon('heroicon-o-user'),
            'user' => RelationshipConstraint::make('user')
                ->label(__('partners::filament/resources/partner.table.filters.responsible'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                )
                ->icon('heroicon-o-user'),
            'title' => RelationshipConstraint::make('title')
                ->label(__('partners::filament/resources/partner.table.filters.title'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                ),
            'company' => RelationshipConstraint::make('company')
                ->label(__('partners::filament/resources/partner.table.filters.company'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                )
                ->icon('heroicon-o-building-office'),
            'industry' => RelationshipConstraint::make('industry')
                ->label(__('partners::filament/resources/partner.table.filters.industry'))
                ->multiple()
                ->selectable(
                    IsRelatedToOperator::make()
                        ->titleAttribute('name')
                        ->searchable()
                        ->multiple()
                        ->preload(),
                )
                ->icon('heroicon-o-building-office'),
        ];

        foreach (Registry::renderTable('filters.reject') as $name) {
            unset($constraints[$name]);
        }

        return collect(array_merge(array_values($constraints), Registry::renderTable('filters.append')))
            ->filter()
            ->values()
            ->all();
    }
}
