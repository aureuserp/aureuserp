<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Models\UOM;

class ManageProducts extends ManageRelatedRecords
{
    protected static string $resource = CategoryResource::class;

    protected static string $relationship = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/category/pages/manage-products.title');
    }

    public function form(Form $form): Form
    {
        return ProductResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ProductResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('products::filament/resources/category/pages/manage-products.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (array $arguments): array {
                        $uom_id = UOM::first()->id;
                        return [
                            'type' => ProductType::GOODS->value,
                            'uom_id' => $uom_id,
                            'uom_po_id' => $uom_id,
                            'category_id' => $this->getOwnerRecord()->id,
                        ];
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/category/pages/manage-products.table.header-actions.create.notification.title'))
                            ->body(__('products::filament/resources/category/pages/manage-products.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
