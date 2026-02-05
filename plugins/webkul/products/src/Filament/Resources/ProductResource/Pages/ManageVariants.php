<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageVariants extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'variants';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/product/pages/manage-variants.title');
    }

    public function form(Schema $schema): Schema
    {
        return ProductResource::form($schema);
    }

    public function table(Table $table): Table
    {
        $table = ProductResource::table($table);

        [$actions] = $table->getActions();

        $flatActions = $actions->getFlatActions();

        if (isset($flatActions['edit'])) {
            $flatActions['edit']
                ->modalWidth(Width::SevenExtraLarge);
        }

        if (isset($flatActions['view'])) {
            $flatActions['view']
                ->modalWidth(Width::SevenExtraLarge)
                ->extraModalFooterActions(fn (Action $action) => [
                    Action::make('print')
                        ->label(__('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.label'))
                        ->color('gray')
                        ->icon('heroicon-o-printer')
                        ->schema([
                            TextInput::make('quantity')
                                ->label(__('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.quantity'))
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100),
                            Radio::make('format')
                                ->label(__('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format'))
                                ->options([
                                    'dymo'       => __('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format-options.dymo'),
                                    '2x7_price'  => __('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format-options.2x7_price'),
                                    '4x7_price'  => __('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format-options.4x7_price'),
                                    '4x12'       => __('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format-options.4x12'),
                                    '4x12_price' => __('products::filament/resources/product/pages/manage-variants.table.actions.view.extra-footer-actions.print.form.fields.format-options.4x12_price'),
                                ])
                                ->default('2x7_price')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            $pdf = PDF::loadView('products::filament.resources.products.actions.print', [
                                'records'  => collect([$record]),
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
                            }, 'Product-'.$record->name.'.pdf');
                        }),
                ]);
        }

        $table->columns(Arr::except($table->getColumns(), ['variants_count']));

        $table->columns([
            TextColumn::make('combinations')
                ->label(__('products::filament/resources/product/pages/manage-variants.table.columns.variant-values'))
                ->state(function ($record) {
                    return $record->combinations->map(function ($combination) {
                        $attributeName = $combination->productAttributeValue?->attribute?->name;
                        $optionName = $combination->productAttributeValue?->attributeOption?->name;

                        return $attributeName && $optionName ? "{$attributeName}: {$optionName}" : $optionName;
                    });
                })
                ->badge()
                ->sortable(),
            ...$table->getColumns(),
        ]);

        $table->modelLabel(__('products::filament/resources/product/pages/manage-variants.title'));

        return $table;
    }

    public function infolist(Schema $schema): Schema
    {
        return ProductResource::infolist($schema);
    }
}
