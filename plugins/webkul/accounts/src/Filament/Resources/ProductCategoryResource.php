<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Account\Models\Category;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Product\Filament\Resources\CategoryResource as BaseProductCategoryResource;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        $schema = BaseProductCategoryResource::form($schema);

        $components = $schema->getComponents();

        $generalComponents = $components[0]->getDefaultChildComponents()[0]->getDefaultChildComponents();

        $generalComponents = array_merge($generalComponents, [
            Fieldset::make()
                ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.label'))
                ->schema([
                    Select::make('property_account_income_id')
                        ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account'))
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account-hint-tooltip'))
                        ->relationship('propertyAccountIncome', 'name')
                        ->preload()
                        ->searchable()
                        ->default(fn (DefaultAccountSettings $settings) => $settings->income_account_id),
                    Select::make('property_account_expense_id')
                        ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account'))
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account-hint-tooltip'))
                        ->relationship('propertyAccountExpense', 'name')
                        ->preload()
                        ->searchable()
                        ->default(fn (DefaultAccountSettings $settings) => $settings->expense_account_id),
                ]),
        ]);

        $components[0]->getDefaultChildComponents()[0]->schema($generalComponents);

        return $schema;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProductCategory::class,
            EditProductCategory::class,
            ManageProducts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListProductCategories::route('/'),
            'create'   => CreateProductCategory::route('/create'),
            'view'     => ViewProductCategory::route('/{record}'),
            'edit'     => EditProductCategory::route('/{record}/edit'),
            'products' => ManageProducts::route('/{record}/products'),
        ];
    }
}
