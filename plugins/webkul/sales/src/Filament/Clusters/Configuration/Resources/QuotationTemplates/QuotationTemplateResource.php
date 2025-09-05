<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Webkul\Sale\Enums\OrderDisplayType;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\OrderTemplateProductResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Pages\CreateQuotationTemplate;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Pages\EditQuotationTemplate;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Pages\ListQuotationTemplates;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Pages\ViewQuotationTemplate;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Schemas\QuotationTemplateForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Schemas\QuotationTemplateInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Tables\QuotationTemplatesTable;
use Webkul\Sale\Models\OrderTemplate;

class QuotationTemplateResource extends Resource
{
    protected static ?string $model = OrderTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/quotation-template.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/quotation-template.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/quotation-template.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListQuotationTemplates::route('/'),
            'create' => CreateQuotationTemplate::route('/create'),
            'view'   => ViewQuotationTemplate::route('/{record}'),
            'edit'   => EditQuotationTemplate::route('/{record}/edit'),
        ];
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('products')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->extraItemActions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->action(function (
                        array $arguments,
                        $livewire
                    ): void {
                        $recordId = explode('-', $arguments['item'])[1];

                        $redirectUrl = OrderTemplateProductResource::getUrl('edit', ['record' => $recordId]);

                        $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
                    }),
            ])
            ->schema([
                Group::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.products'))
                                    ->label('Product')
                                    ->required(),
                                TextInput::make('name')
                                    ->live(onBlur: true)
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name'))
                                    ->label('Name'),
                                TextInput::make('quantity')
                                    ->required()
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity')),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function getSectionRepeater(): Repeater
    {
        return Repeater::make('sections')
            ->relationship('sections')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->extraItemActions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->action(function (
                        array $arguments,
                        $livewire
                    ): void {
                        $recordId = explode('-', $arguments['item'])[1];

                        $redirectUrl = OrderTemplateProductResource::getUrl('edit', ['record' => $recordId]);

                        $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
                    }),
            ])
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('name')
                            ->live(onBlur: true)
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name')),
                        Hidden::make('quantity')
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity'))
                            ->default(0),
                        Hidden::make('display_type')
                            ->required()
                            ->default(OrderDisplayType::SECTION->value),
                    ]),
            ]);
    }

    public static function getNoteRepeater(): Repeater
    {
        return Repeater::make('notes')
            ->relationship('notes')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->extraItemActions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->action(function (
                        array $arguments,
                        $livewire
                    ): void {
                        $recordId = explode('-', $arguments['item'])[1];

                        $redirectUrl = OrderTemplateProductResource::getUrl('edit', ['record' => $recordId]);

                        $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
                    }),
            ])
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('name')
                            ->live(onBlur: true)
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name')),
                        Hidden::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity'))
                            ->required()
                            ->default(0),
                        Hidden::make('display_type')
                            ->required()
                            ->default(OrderDisplayType::NOTE->value),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationTemplateInfolist::configure($schema);
    }
}
