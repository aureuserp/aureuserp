<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Actions\GenerateVariantsAction;
use Webkul\Product\Models\ProductAttribute;

class ManageAttributes extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'attributes';

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/product/pages/manage-attributes.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attribute_id')
                    ->label(__('products::filament/resources/product/pages/manage-attributes.form.attribute'))
                    ->required()
                    ->relationship(
                        'attribute',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function (string $value) {
                        return $this->getOwnerRecord()->attributes->contains('attribute_id', $value);
                    })
                    ->searchable()
                    ->preload()
                    ->disabledOn('edit')
                    ->createOptionForm(fn (Forms\Form $form): Form => AttributeResource::form($form))
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('options', []);
                    }),
                Forms\Components\Select::make('options')
                    ->label(__('products::filament/resources/product/pages/manage-attributes.form.values'))
                    ->required()
                    ->relationship(
                        name: 'options',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('products_attribute_options.attribute_id', $get('attribute_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->description(__('products::filament/resources/product/pages/manage-attributes.table.description'))
            ->columns([
                Tables\Columns\TextColumn::make('attribute.name')
                    ->label(__('products::filament/resources/product/pages/manage-attributes.table.columns.attribute')),
                Tables\Columns\TextColumn::make('values.attributeOption.name')
                    ->label(__('products::filament/resources/product/pages/manage-attributes.table.columns.values'))
                    ->badge(),
            ])
            ->headerActions([
                GenerateVariantsAction::make(),
                Tables\Actions\CreateAction::make()
                    ->label(__('products::filament/resources/product/pages/manage-attributes.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->after(function (ProductAttribute $record) {
                        $this->updateOrCreateVariants($record);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/product/pages/manage-attributes.table.header-actions.create.notification.title'))
                            ->body(__('products::filament/resources/product/pages/manage-attributes.table.header-actions.create.notification.body')),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (ProductAttribute $record) {
                        $this->updateOrCreateVariants($record);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/product/pages/manage-attributes.table.actions.edit.notification.title'))
                            ->body(__('products::filament/resources/product/pages/manage-attributes.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->after(function (ProductAttribute $record) {
                        $this->updateOrCreateVariants($record);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/product/pages/manage-attributes.table.actions.delete.notification.title'))
                            ->body(__('products::filament/resources/product/pages/manage-attributes.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }

    protected function updateOrCreateVariants(ProductAttribute $record): void
    {
        $record->values->each(function ($value) use ($record) {
            $value->update([
                'extra_price'  => $value->attributeOption->extra_price,
                'attribute_id' => $record->attribute_id,
                'product_id'   => $record->product_id,
            ]);
        });

        $this->replaceMountedTableAction('products.generate.variants');
    }
}
