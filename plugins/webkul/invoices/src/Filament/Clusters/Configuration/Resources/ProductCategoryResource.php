<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;
use Webkul\Product\Filament\Resources\CategoryResource as BaseProductCategoryResource;
use Webkul\Invoice\Models\Category;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/product-category.form.sections.fields.name'))
                            ->maxLength(255),
                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'full_name')
                            ->label(__('sales::filament/clusters/configurations/resources/product-category.form.sections.fields.parent-category')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = BaseProductCategoryResource::table($table);

        $table->filters([
            Tables\Filters\QueryBuilder::make()
                ->constraintPickerColumns(2)
                ->constraints([
                    Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.name'))
                        ->icon('heroicon-o-squares-2x2'),
                    Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('full_name')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.complete-name'))
                        ->icon('heroicon-o-squares-2x2'),
                    Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('parent_id')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.parent-category'))
                        ->icon('heroicon-o-folder')
                        ->multiple()
                        ->selectable(
                            IsRelatedToOperator::make()
                                ->titleAttribute('full_name')
                                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.parent-category'))
                                ->searchable()
                                ->multiple()
                                ->preload(),
                        ),
                    Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator_id')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.created-by'))
                        ->icon('heroicon-o-user')
                        ->multiple()
                        ->selectable(
                            IsRelatedToOperator::make()
                                ->titleAttribute('name')
                                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.created-by'))
                                ->searchable()
                                ->multiple()
                                ->preload(),
                        ),
                    Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.created-at')),
                    Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                        ->label(__('sales::filament/clusters/configurations/resources/product-category.table.filters.updated-at')),
                ]),
        ]);

        $table->groups([
            Tables\Grouping\Group::make('name')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.name'))
                ->collapsible(),
            Tables\Grouping\Group::make('full_name')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.complete-name'))
                ->collapsible(),
            Tables\Grouping\Group::make('parent.full_name')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.parent-complete-name'))
                ->collapsible(),
            Tables\Grouping\Group::make('createdBy.name')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.created-by'))
                ->collapsible(),
            Tables\Grouping\Group::make('created_at')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.created-at'))
                ->date()
                ->collapsible(),
            Tables\Grouping\Group::make('updated_at')
                ->label(__('sales::filament/clusters/configurations/resources/product-category.table.groups.updated-at'))
                ->date()
                ->collapsible(),
        ]);

        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'view'   => Pages\ViewProductCategory::route('/{record}'),
            'edit'   => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
