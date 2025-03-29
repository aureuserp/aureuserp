<?php

namespace Webkul\Product\Filament\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('products::filament/resources/product.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                Forms\Components\RichEditor::make('description')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.description')),
                                Forms\Components\Select::make('tags')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                            ->required()
                                            ->unique('products_tags'),
                                    ]),
                            ]),

                        Forms\Components\Section::make(__('products::filament/resources/product.form.sections.images.title'))
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->multiple()
                                    ->storeFileNamesIn('products'),
                            ]),

                        Forms\Components\Section::make(__('products::filament/resources/product.form.sections.inventory.title'))
                            ->schema([
                                Forms\Components\Fieldset::make(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('weight')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                                            ->numeric()
                                            ->minValue(0),
                                        Forms\Components\TextInput::make('volume')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                                            ->numeric()
                                            ->minValue(0),
                                    ]),
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('type') == ProductType::GOODS->value),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('products::filament/resources/product.form.sections.settings.title'))
                            ->schema([
                                Forms\Components\Radio::make('type')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                                    ->options(ProductType::class)
                                    ->default(ProductType::GOODS->value)
                                    ->live(),
                                Forms\Components\TextInput::make('reference')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.reference'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('barcode')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.barcode'))
                                    ->maxLength(255),
                                Forms\Components\Select::make('category_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.category'))
                                    ->required()
                                    ->relationship('category', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Category::first()?->id)
                                    ->createOptionForm(fn (Forms\Form $form): Form => CategoryResource::form($form)),
                                Forms\Components\Select::make('company_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->default_company_id),
                            ]),

                        Forms\Components\Section::make(__('products::filament/resources/product.form.sections.pricing.title'))
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.price'))
                                    ->numeric()
                                    ->required()
                                    ->default(0.00),
                                Forms\Components\TextInput::make('cost')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.cost'))
                                    ->numeric()
                                    ->default(0.00),
                                Forms\Components\Hidden::make('uom_id')
                                    ->default(UOM::first()->id),
                                Forms\Components\Hidden::make('uom_po_id')
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
            ->columns([
                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('')
                    ->icon(fn (Product $record): string => $record->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Product $record): string => $record->is_favorite ? 'warning' : 'gray')
                    ->action(function (Product $record): void {
                        $record->update([
                            'is_favorite' => ! $record->is_favorite,
                        ]);
                    }),
                Tables\Columns\ImageColumn::make('images')
                    ->label(__('products::filament/resources/product.table.columns.images'))
                    ->placeholder('—')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('products::filament/resources/product.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label(__('products::filament/resources/product.table.columns.variants'))
                    ->placeholder('—')
                    ->searchable()
                    ->counts('variants')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('products::filament/resources/product.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->label(__('products::filament/resources/product.table.columns.tags'))
                    ->placeholder('—')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('responsible.name')
                    ->label(__('products::filament/resources/product.table.columns.responsible'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('barcode')
                    ->label(__('products::filament/resources/product.table.columns.barcode'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('products::filament/resources/product.table.columns.company'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('products::filament/resources/product.table.columns.price'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label(__('products::filament/resources/product.table.columns.cost'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('products::filament/resources/product.table.columns.category'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('products::filament/resources/product.table.columns.type'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('products::filament/resources/product.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('products::filament/resources/product.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                Tables\Filters\QueryBuilder::make()
                    ->constraints(collect([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('products::filament/resources/product.table.filters.name')),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('reference')
                            ->label(__('products::filament/resources/product.table.filters.reference'))
                            ->icon('heroicon-o-link'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('barcode')
                            ->label(__('products::filament/resources/product.table.filters.barcode'))
                            ->icon('heroicon-o-bars-4'),
                        Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::make('is_favorite')
                            ->label(__('products::filament/resources/product.table.filters.is-favorite'))
                            ->icon('heroicon-o-star'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('price')
                            ->label(__('products::filament/resources/product.table.filters.price'))
                            ->icon('heroicon-o-banknotes'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('cost')
                            ->label(__('products::filament/resources/product.table.filters.cost'))
                            ->icon('heroicon-o-banknotes'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('weight')
                            ->label(__('products::filament/resources/product.table.filters.weight'))
                            ->icon('heroicon-o-scale'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('volume')
                            ->label(__('products::filament/resources/product.table.filters.volume'))
                            ->icon('heroicon-o-beaker'),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('type')
                            ->label(__('products::filament/resources/product.table.filters.type'))
                            ->multiple()
                            ->options(ProductType::class)
                            ->icon('heroicon-o-queue-list'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('tags')
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('products::filament/resources/product.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('products::filament/resources/product.table.filters.updated-at')),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('responsible')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
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
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.actions.restore.notification.title'))
                                ->body(__('products::filament/resources/product.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.actions.delete.notification.title'))
                                ->body(__('products::filament/resources/product.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->action(function (Product $record) {
                            try {
                                $record->forceDelete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/product.table.actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/product.table.actions.force-delete.notification.error.body'))
                                    ->send();
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('print')
                        ->label(__('products::filament/resources/product.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->form([
                            Forms\Components\TextInput::make('quantity')
                                ->label(__('products::filament/resources/product.table.bulk-actions.print.form.fields.quantity'))
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100),
                            Forms\Components\Radio::make('format')
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
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/product.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/product.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/product.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/product.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.name')),

                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('tags.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->separator(', ')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                            ]),

                        Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.images.title'))
                            ->schema([
                                Infolists\Components\ImageEntry::make('images')
                                    ->hiddenLabel()
                                    ->circular(),
                            ])
                            ->visible(fn ($record): bool => ! empty($record->images)),

                        Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.inventory.title'))
                            ->schema([
                                Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        Infolists\Components\Grid::make(2)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('weight')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-scale'),

                                                Infolists\Components\TextEntry::make('volume')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-beaker'),
                                            ]),
                                    ]),
                            ])
                            ->visible(fn ($record): bool => $record->type == ProductType::GOODS),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.settings.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.type'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-queue-list'),

                                Infolists\Components\TextEntry::make('reference')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.reference'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-identification'),

                                Infolists\Components\TextEntry::make('barcode')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.barcode'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-bars-4'),

                                Infolists\Components\TextEntry::make('category.full_name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.category'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-folder'),

                                Infolists\Components\TextEntry::make('company.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.company'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Infolists\Components\Section::make(__('products::filament/resources/product.infolist.sections.pricing.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('price')
                                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.price'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('cost')
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
