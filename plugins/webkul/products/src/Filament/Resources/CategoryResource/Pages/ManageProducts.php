<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageProducts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = CategoryResource::class;

    protected static string $relationship = 'products';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/category/pages/manage-products.title');
    }

    public function form(Schema $schema): Schema
    {
        return ProductResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ProductResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->label(__('products::filament/resources/category/pages/manage-products.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (array $arguments): array {
                        $uom_id = UOM::first()->id;

                        return [
                            'type'        => ProductType::GOODS->value,
                            'uom_id'      => $uom_id,
                            'uom_po_id'   => $uom_id,
                            'category_id' => $this->getOwnerRecord()->id,
                        ];
                    })
                    ->mutateDataUsing(function (array $data): array {
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
