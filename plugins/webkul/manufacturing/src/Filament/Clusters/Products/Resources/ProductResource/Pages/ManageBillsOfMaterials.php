<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBillsOfMaterials extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;
    use HasRepeaterColumnManager;

    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'billsOfMaterials';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/products/resources/product/pages/bill-of-materials.navigation.title');
    }

    public function form(Schema $schema): Schema
    {
        return BillsOfMaterialResource::form($schema);
    }

    public function table(Table $table): Table
    {
        $table = BillsOfMaterialResource::table($table);

        return $table
            ->headerActions([
                CreateAction::make()
                    ->label(__('manufacturing::filament/clusters/products/resources/product/pages/bill-of-materials.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (): array {
                        return [
                            'product_id'         => $this->getOwnerRecord()->id,
                            'uom_id'             => $this->getOwnerRecord()->uom_id,
                            'company_id'         => $this->getOwnerRecord()->company_id,
                            'quantity'           => 1,
                            'type'               => BillOfMaterialType::NORMAL->value,
                            'ready_to_produce'   => BillOfMaterialReadyToProduce::ALL_AVAILABLE->value,
                            'consumption'        => BillOfMaterialConsumption::WARNING->value,
                            'produce_delay'      => 0,
                            'days_to_prepare_mo' => 0,
                        ];
                    })
                    ->mutateDataUsing(function (array $data): array {
                        $data['product_id'] ??= $this->getOwnerRecord()->id;
                        $data['uom_id'] ??= $this->getOwnerRecord()->uom_id;
                        $data['company_id'] ??= $this->getOwnerRecord()->company_id;

                        return BillsOfMaterialResource::normalizeProductVariantData($data);
                    })
                    ->modalWidth(Width::SevenExtraLarge)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/products/resources/product/pages/bill-of-materials.header-actions.create.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/products/resources/product/pages/bill-of-materials.header-actions.create.notification.success.body')),
                    ),
            ]);
    }
}
