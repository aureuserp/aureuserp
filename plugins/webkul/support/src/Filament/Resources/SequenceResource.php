<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Filament\Resources\SequenceResource\Pages\ManageSequences;
use Webkul\Support\Models\Sequence;

class SequenceResource extends Resource
{
    protected static ?string $model = Sequence::class;

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('support::filament/resources/sequence.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('support::filament/resources/sequence.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/sequence.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/resources/sequence.form.sections.general.title'))
                    ->description(fn (?Sequence $record): ?string => $record === null
                        ? __('support::filament/resources/sequence.form.sections.general.description')
                        : null)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.name'))
                            ->maxLength(255),
                        TextEntry::make('applies_to')
                            ->label(__('support::filament/resources/sequence.table.columns.applies-to'))
                            ->state(fn (Sequence $record): string => (string) static::scopeLabel($record))
                            ->visible(fn (?Sequence $record): bool => $record !== null && filled($record->scope_type)),
                        TextInput::make('code')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.code'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.general.fields.code-help'))
                            ->required(fn (?Sequence $record): bool => $record === null)
                            ->maxLength(255)
                            ->visible(fn (?Sequence $record): bool => $record === null || filled($record->code))
                            ->disabled(fn (?Sequence $record): bool => $record !== null)
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, Get $get): Unique {
                                    $companyId = $get('company_id');

                                    return $companyId
                                        ? $rule->where('company_id', $companyId)
                                        : $rule->whereNull('company_id');
                                },
                            ),
                        Select::make('company_id')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(fn (?Sequence $record): bool => $record !== null),
                    ])
                    ->columns(2),
                Section::make(__('support::filament/resources/sequence.form.sections.format.title'))
                    ->schema([
                        TextInput::make('prefix')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.prefix'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.format.fields.prefix-help'))
                            ->maxLength(255),
                        TextInput::make('suffix')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.suffix'))
                            ->maxLength(255),
                        TextInput::make('padding')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.padding'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(5)
                            ->required(),
                        TextInput::make('next_number')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.next-number'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.format.fields.next-number-help'))
                            ->integer()
                            ->minValue(fn (?Sequence $record): int => max(1, $record?->next_number ?? 1))
                            ->default(1)
                            ->required(),
                        TextInput::make('step')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.step'))
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        Select::make('reset_frequency')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.reset-frequency'))
                            ->options(SequenceResetFrequency::options())
                            ->default(SequenceResetFrequency::NEVER->value)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['company', 'scope']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('support::filament/resources/sequence.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('code')
                    ->label(__('support::filament/resources/sequence.table.columns.code'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('scope')
                    ->label(__('support::filament/resources/sequence.table.columns.applies-to'))
                    ->state(fn (Sequence $record): ?string => static::scopeLabel($record))
                    ->placeholder('—'),
                TextColumn::make('company.name')
                    ->label(__('support::filament/resources/sequence.table.columns.company'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('next_preview')
                    ->label(__('support::filament/resources/sequence.table.columns.next-preview'))
                    ->state(fn (Sequence $record): string => $record->formatNumber(max(1, $record->next_number)))
                    ->badge()
                    ->color('info'),
                TextColumn::make('next_number')
                    ->label(__('support::filament/resources/sequence.table.columns.next-number'))
                    ->sortable(),
                TextColumn::make('reset_frequency')
                    ->label(__('support::filament/resources/sequence.table.columns.reset-frequency'))
                    ->formatStateUsing(fn (SequenceResetFrequency $state): string => SequenceResetFrequency::options()[$state->value])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('support::filament/resources/sequence.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/sequence.table.actions.edit.notification.title'))
                            ->body(__('support::filament/resources/sequence.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/sequence.table.actions.delete.notification.title'))
                            ->body(__('support::filament/resources/sequence.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/sequence.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/sequence.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('code');
    }

    public static function scopeLabel(Sequence $record): ?string
    {
        if (! $record->scope_type) {
            return null;
        }

        $label = $record->scope?->name ?? "#{$record->scope_id}";

        if ($record->variant) {
            $label .= ' — '.__("support::filament/resources/sequence.table.variants.{$record->variant}");
        }

        return $label;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSequences::route('/'),
        ];
    }
}
