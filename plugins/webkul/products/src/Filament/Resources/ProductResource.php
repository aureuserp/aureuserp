<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('products::filament/resources/product.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                RichEditor::make('description')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.description')),
                                Select::make('tags')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('products_tags'),
                                    ]),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.images.title'))
                            ->schema([
                                FileUpload::make('images')
                                    ->image()
                                    ->multiple()
                                    ->storeFileNamesIn('products'),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.inventory.title'))
                            ->schema([
                                Fieldset::make(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        TextInput::make('weight')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999),
                                        TextInput::make('volume')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999),
                                    ]),
                            ])
                            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS->value),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/product.form.sections.settings.title'))
                            ->schema([
                                Radio::make('type')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                                    ->options(ProductType::class)
                                    ->default(ProductType::GOODS->value)
                                    ->live(),
                                TextInput::make('reference')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.reference'))
                                    ->maxLength(255),
                                TextInput::make('barcode')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.barcode'))
                                    ->maxLength(255),
                                Select::make('category_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.category'))
                                    ->required()
                                    ->relationship('category', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Category::first()?->id)
                                    ->createOptionForm(fn (Schema $schema): Schema => CategoryResource::form($schema)),
                                Select::make('company_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.company'))
                                    ->relationship(
                                        'company',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->default_company_id),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.pricing.title'))
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.price'))
                                    ->numeric()
                                    ->required()
                                    ->default(0.00)
                                    ->minValue(0),
                                TextInput::make('cost')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.cost'))
                                    ->numeric()
                                    ->default(0.00)
                                    ->minValue(0),
                                Hidden::make('uom_id')
                                    ->default(UOM::first()->id),
                                Hidden::make('uom_po_id')
                                    ->default(UOM::first()->id),
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
            ->columnManagerColumns(2)
            ->columns([
                IconColumn::make('is_favorite')
                    ->label(__('products::filament/resources/product.table.columns.favorite'))
                    ->icon(fn (Product $record): string => $record->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Product $record): string => $record->is_favorite ? 'warning' : 'gray')
                    ->action(function (Product $record): void {
                        $record->update([
                            'is_favorite' => ! $record->is_favorite,
                        ]);
                    }),
                ImageColumn::make('images')
                    ->label(__('products::filament/resources/product.table.columns.images'))
                    ->placeholder('—')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),
                TextColumn::make('name')
                    ->label(__('products::filament/resources/product.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variants_count')
                    ->label(__('products::filament/resources/product.table.columns.variants'))
                    ->placeholder('—')
                    ->counts('variants')
                    ->sortable(),
                TextColumn::make('reference')
                    ->label(__('products::filament/resources/product.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags.name')
                    ->label(__('products::filament/resources/product.table.columns.tags'))
                    ->placeholder('—')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('responsible.name')
                    ->label(__('products::filament/resources/product.table.columns.responsible'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('barcode')
                    ->label(__('products::filament/resources/product.table.columns.barcode'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('products::filament/resources/product.table.columns.company'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label(__('products::filament/resources/product.table.columns.price'))
                    ->sortable(),
                TextColumn::make('cost')
                    ->label(__('products::filament/resources/product.table.columns.cost'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('products::filament/resources/product.table.columns.category'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->label(__('products::filament/resources/product.table.columns.type'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('products::filament/resources/product.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/product.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/product.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('type')
                    ->label(__('products::filament/resources/product.table.groups.type')),
                Tables\Grouping\Group::make('category.name')
                    ->label(__('products::filament/resources/product.table.groups.category')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('products::filament/resources/product.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                        TextConstraint::make('name')
                            ->label(__('products::filament/resources/product.table.filters.name')),
                        TextConstraint::make('reference')
                            ->label(__('products::filament/resources/product.table.filters.reference'))
                            ->icon('heroicon-o-link'),
                        TextConstraint::make('barcode')
                            ->label(__('products::filament/resources/product.table.filters.barcode'))
                            ->icon('heroicon-o-bars-4'),
                        BooleanConstraint::make('is_favorite')
                            ->label(__('products::filament/resources/product.table.filters.is-favorite'))
                            ->icon('heroicon-o-star'),
                        NumberConstraint::make('price')
                            ->label(__('products::filament/resources/product.table.filters.price'))
                            ->icon('heroicon-o-banknotes'),
                        NumberConstraint::make('cost')
                            ->label(__('products::filament/resources/product.table.filters.cost'))
                            ->icon('heroicon-o-banknotes'),
                        NumberConstraint::make('weight')
                            ->label(__('products::filament/resources/product.table.filters.weight'))
                            ->icon('heroicon-o-scale'),
                        NumberConstraint::make('volume')
                            ->label(__('products::filament/resources/product.table.filters.volume'))
                            ->icon('heroicon-o-beaker'),
                        SelectConstraint::make('type')
                            ->label(__('products::filament/resources/product.table.filters.type'))
                            ->multiple()
                            ->options(ProductType::class)
                            ->icon('heroicon-o-queue-list'),
                        RelationshipConstraint::make('tags')
                            ->label(__('products::filament/resources/product.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                        DateConstraint::make('created_at')
                            ->label(__('products::filament/resources/product.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('products::filament/resources/product.table.filters.updated-at')),
                        RelationshipConstraint::make('responsible')
                            ->label(__('products::filament/resources/product.table.filters.responsible'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('company')
                            ->label(__('products::filament/resources/product.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('products::filament/resources/product.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.actions.restore.notification.title'))
                                ->body(__('products::filament/resources/product.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.actions.delete.notification.title'))
                                ->body(__('products::filament/resources/product.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (ForceDeleteAction $action, Product $record) {
                            try {
                                $record->forceDelete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/product.table.actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/product.table.actions.force-delete.notification.error.body'))
                                    ->send();
                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.actions.force-delete.notification.success.title'))
                                ->body(__('products::filament/resources/product.table.actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('products::filament/resources/product.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->schema([
                            TextInput::make('quantity')
                                ->label(__('products::filament/resources/product.table.bulk-actions.print.form.fields.quantity'))
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100),
                            Radio::make('format')
                                ->label(__('products::filament/resources/product.table.bulk-actions.print.form.fields.format'))
                                ->options([
                                    'dymo'       => __('products::filament/resources/product.table.bulk-actions.print.form.fields.format-options.dymo'),
                                    '2x7_price'  => __('products::filament/resources/product.table.bulk-actions.print.form.fields.format-options.2x7_price'),
                                    '4x7_price'  => __('products::filament/resources/product.table.bulk-actions.print.form.fields.format-options.4x7_price'),
                                    '4x12'       => __('products::filament/resources/product.table.bulk-actions.print.form.fields.format-options.4x12'),
                                    '4x12_price' => __('products::filament/resources/product.table.bulk-actions.print.form.fields.format-options.4x12_price'),
                                ])
                                ->default('2x7_price')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $pdf = PDF::loadView('products::filament.resources.products.actions.print', [
                                'records'  => $records,
                                'quantity' => $data['quantity'],
                                'format'   => $data['format'],
                            ]);

                            $paperSize = match ($data['format']) {
                                'dymo'  => [0, 0, 252.2, 144],
                                default => 'a4',
                            };

                            $pdf->setPaper($paperSize, 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Product-Barcode.pdf');
                        }),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/product.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/product.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.name')),

                                TextEntry::make('description')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                TextEntry::make('tags.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->separator(', ')
                                    ->weight(FontWeight::Bold),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.images.title'))
                            ->schema([
                                ImageEntry::make('images')
                                    ->hiddenLabel(),
                            ])
                            ->visible(fn ($record): bool => ! empty($record->images)),

                        Section::make(__('products::filament/resources/product.infolist.sections.inventory.title'))
                            ->schema([
                                Section::make(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('weight')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-scale'),

                                                TextEntry::make('volume')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-beaker'),
                                            ]),
                                    ]),
                            ])
                            ->visible(fn ($record): bool => $record->type == ProductType::GOODS),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/product.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('type')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.type'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-queue-list'),

                                TextEntry::make('reference')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.reference'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-identification'),

                                TextEntry::make('barcode')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.barcode'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-bars-4'),

                                TextEntry::make('category.full_name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.category'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-folder'),

                                TextEntry::make('company.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.company'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.pricing.title'))
                            ->schema([
                                TextEntry::make('price')
                                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.price'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-banknotes'),

                                TextEntry::make('cost')
                                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.cost'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-banknotes'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
