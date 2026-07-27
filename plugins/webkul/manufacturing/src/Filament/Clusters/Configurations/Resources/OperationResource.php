<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\CreateOperation;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\EditOperation;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\ListOperations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\ViewOperation;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\CreateBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\EditBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Product\Models\ProductAttributeValue;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/operation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/operation.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/operation.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                Select::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.bill-of-material'))
                                    ->relationship('billOfMaterial', 'code')
                                    ->hiddenOn([
                                        CreateBillOfMaterial::class,
                                        EditBillOfMaterial::class,
                                        ManageBillsOfMaterials::class,
                                    ])
                                    ->getOptionLabelFromRecordUsing(fn (BillOfMaterial $record): string => static::getBillOfMaterialLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('attributeValues', []))
                                    ->required(),

                                Select::make('work_center_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.work-center'))
                                    ->relationship('workCenter', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm(fn (Schema $schema): Schema => WorkCenterResource::form($schema))
                                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),

                                Select::make('attributeValues')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.apply-on-variants'))
                                    ->relationship(
                                        'attributeValues',
                                        'id',
                                        modifyQueryUsing: function (Get $get, Builder $query): void {
                                            $billOfMaterial = BillOfMaterial::query()
                                                ->with('product')
                                                ->find($get('bill_of_material_id'));

                                            $productId = $billOfMaterial?->product_id;

                                            if (! $productId) {
                                                $query->whereRaw('1 = 0');

                                                return;
                                            }

                                            $query->where('product_id', $productId);
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (ProductAttributeValue $record): string => $record->attributeOption?->name ?? (string) $record->id)
                                    ->searchable()
                                    ->preload()
                                    ->multiple(),

                                Select::make('blockedByOperations')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.blocked-by'))
                                    ->relationship(
                                        'blockedByOperations',
                                        'name',
                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('id', '!=', $get('id'))->where('bill_of_material_id', $get('bill_of_material_id'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->multiple(),

                                TextEntry::make('company')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.company'))
                                    ->state(function (Get $get): string {
                                        $billOfMaterial = BillOfMaterial::query()
                                            ->with('company')
                                            ->find($get('bill_of_material_id'));

                                        return $billOfMaterial?->company?->name ?? '—';
                                    }),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.title'))
                            ->schema([
                                Radio::make('worksheet_type')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.worksheet'))
                                    ->options(OperationWorksheetType::class)
                                    ->default(OperationWorksheetType::TEXT->value)
                                    ->inline(false)
                                    ->live()
                                    ->required()
                                    ->columnSpanFull(),

                                FileUpload::make('worksheet')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.pdf'))
                                    ->disk('public')
                                    ->directory('manufacturing/operations/worksheets')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->openable()
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::PDF))
                                    ->columnSpanFull(),

                                TextInput::make('worksheet_google_slide_url')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.google-slide'))
                                    ->url()
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.google-slide-placeholder'))
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::GOOGLE_SLIDE))
                                    ->columnSpanFull(),

                                Textarea::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.description'))
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.description-placeholder'))
                                    ->rows(6)
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::TEXT))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.title'))
                            ->schema([
                                Radio::make('time_mode')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode'))
                                    ->options(OperationTimeMode::class)
                                    ->default(OperationTimeMode::MANUAL->value)
                                    ->inline(false)
                                    ->live()
                                    ->required(),

                                TextInput::make('time_mode_batch')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch'))
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->step('1')
                                    ->prefix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch-prefix'))
                                    ->suffix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch-suffix'))
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('time_mode'), OperationTimeMode::AUTO)),

                                TextInput::make('manual_cycle_time')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.manual-cycle-time'))
                                    ->default('60:00')
                                    ->rule('regex:/^\d+:\d{2}$/')
                                    ->placeholder('60:00')
                                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                        $component->state(format_float_time($state ?? 60, 'minutes'));
                                    })
                                    ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes'))
                                    ->suffix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.manual-cycle-time-suffix')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->modifyQueryUsing(fn (Builder $query) => $query->with('billOfMaterial'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.name'))
                    ->searchable(),
                TextColumn::make('bill_of_material_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.bill-of-material'))
                    ->formatStateUsing(function (mixed $state, Operation $record): string {
                        return static::getBillOfMaterialLabel($record->billOfMaterial);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('billOfMaterial', function (Builder $billOfMaterialQuery) use ($search): void {
                            $billOfMaterialQuery
                                ->whereLike('code', "%{$search}%")
                                ->orWhereHas('product', fn (Builder $productQuery) => $productQuery->whereLike('name', "%{$search}%"));
                        });
                    }),
                TextColumn::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.work-center'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('time_mode')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.time-mode'))
                    ->badge(),
                TextColumn::make('manual_cycle_time')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.manual-cycle-time'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 60, 'minutes'))
                    ->toggleable(),
                TextColumn::make('worksheet_type')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.worksheet-type'))
                    ->badge(),
                TextColumn::make('deleted_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('work_center_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.work-center'))
                    ->relationship('workCenter', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('time_mode')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.time-mode'))
                    ->options(OperationTimeMode::options()),
                SelectFilter::make('worksheet_type')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.worksheet-type'))
                    ->options(OperationWorksheetType::options()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (Operation $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (Operation $record): bool => $record->trashed())
                    ->modalWidth(Width::SevenExtraLarge),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.restore.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.delete.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Operation $record, ForceDeleteAction $action): void {
                        try {
                            $record->forceDelete();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.error.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.error.body'))
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.restore.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.delete.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                $records->each(fn (Model $record): ?bool => $record->forceDelete());
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-cog-8-tooth')
                                    ->columnSpanFull(),
                                TextEntry::make('billOfMaterial.code')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.bill-of-material'))
                                    ->formatStateUsing(function (?string $state, Operation $record): string {
                                        return static::getBillOfMaterialLabel($record->billOfMaterial);
                                    }),
                                TextEntry::make('workCenter.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.work-center'))
                                    ->placeholder('—'),
                                TextEntry::make('attributeValues.attributeOption.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.apply-on-variants'))
                                    ->badge()
                                    ->separator(',')
                                    ->placeholder('—'),
                                TextEntry::make('billOfMaterial.company.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.company'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.title'))
                            ->schema([
                                TextEntry::make('worksheet_type')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.worksheet'))
                                    ->badge(),
                                TextEntry::make('worksheet')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.pdf'))
                                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '—')
                                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::PDF && filled($record->worksheet)),
                                TextEntry::make('worksheet_google_slide_url')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.google-slide'))
                                    ->url(fn (?string $state): ?string => $state)
                                    ->openUrlInNewTab()
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::GOOGLE_SLIDE),
                                TextEntry::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.description'))
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::TEXT && filled($record->note)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('time_mode')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.time-mode'))
                                    ->badge(),
                                TextEntry::make('time_mode_batch')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.time-mode-batch'))
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->time_mode === OperationTimeMode::AUTO),
                                TextEntry::make('manual_cycle_time')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.manual-cycle-time'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 60, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.manual-cycle-time-suffix'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.created-by'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('created_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOperations::route('/'),
            'create' => CreateOperation::route('/create'),
            'view'   => ViewOperation::route('/{record}'),
            'edit'   => EditOperation::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewOperation::class,
            EditOperation::class,
        ]);
    }

    protected static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        return $billOfMaterial->code
            ?: ($billOfMaterial->product?->name ?? (string) $billOfMaterial->getKey());
    }

    protected static function matchesEnumState(mixed $state, BackedEnum $enum): bool
    {
        if ($state instanceof BackedEnum) {
            return $state->value === $enum->value;
        }

        return $state === $enum->value;
    }
}
