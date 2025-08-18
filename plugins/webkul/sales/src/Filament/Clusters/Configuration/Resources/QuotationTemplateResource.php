<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Webkul\Sale\Enums\OrderDisplayType;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages\CreateQuotationTemplate;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages\EditQuotationTemplate;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages\ListQuotationTemplates;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages\ViewQuotationTemplate;
use Webkul\Sale\Models\OrderTemplate;

class QuotationTemplateResource extends Resource
{
    protected static ?string $model = OrderTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

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
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.title'))
                                            ->schema([
                                                static::getProductRepeater(),
                                                static::getSectionRepeater(),
                                                static::getNoteRepeater(),
                                            ]),
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                RichEditor::make('note')
                                                    ->placeholder(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.note-placeholder'))
                                                    ->hiddenLabel(),
                                            ]),
                                    ])
                                    ->persistTabInQueryString(),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.title'))
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.name'))
                                                    ->required(),
                                                TextInput::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.quotation-validity'))
                                                    ->default(0)
                                                    ->required(),
                                                Select::make('journal_id')
                                                    ->relationship('journal', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.sale-journal'))
                                                    ->required(),
                                            ])->columns(1),
                                    ]),
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.title'))
                                            ->schema([
                                                Toggle::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-signature')),
                                                Toggle::make('require_payment')
                                                    ->live()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-payment')),
                                                TextInput::make('prepayment_percentage')
                                                    ->prefix('of')
                                                    ->suffix('%')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.prepayment-percentage'))
                                                    ->visible(fn (Get $get) => $get('require_payment') === true),
                                            ])->columns(1),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('createdBy.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.created-by'))
                    ->label(__('Created By')),
                TextColumn::make('company.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.company')),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.name')),
                TextColumn::make('number_of_days')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.number-of-days')),
                TextColumn::make('journal.name')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.journal')),
                IconColumn::make('require_signature')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.signature-required')),
                IconColumn::make('require_payment')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.payment-required')),
                TextColumn::make('prepayment_percentage')
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.prepayment-percentage')),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('createdBy.name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-by'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company.name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.company'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.journal'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.bulk-actions.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.bulk-actions.notification.body'))
                        ),
                ]),
            ]);
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
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make('Tabs')
                                    ->tabs([
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.products.title'))
                                            ->schema([
                                                RepeatableEntry::make('products')
                                                    ->hiddenLabel()
                                                    ->schema([
                                                        TextEntry::make('product.name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.product'))
                                                            ->icon('heroicon-o-shopping-bag'),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                        TextEntry::make('quantity')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quantity'))
                                                            ->numeric(),
                                                        TextEntry::make('unit-price')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.unit-price'))
                                                            ->money('USD'),
                                                    ])
                                                    ->columns(4),

                                                RepeatableEntry::make('sections')
                                                    ->hiddenLabel()
                                                    ->hidden(fn ($record) => $record->sections->isEmpty())
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.section-name')),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),

                                                RepeatableEntry::make('notes')
                                                    ->hiddenLabel()
                                                    ->hidden(fn ($record) => $record->notes->isEmpty())
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.note-title')),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),
                                            ]),
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                TextEntry::make('note')
                                                    ->markdown()
                                                    ->hiddenLabel()
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->persistTabInQueryString(),
                            ])->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.general.title'))
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.name'))
                                                    ->icon('heroicon-o-document-text'),
                                                TextEntry::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quotation-validity'))
                                                    ->suffix(' days')
                                                    ->icon('heroicon-o-calendar'),
                                                TextEntry::make('journal.name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.sale-journal'))
                                                    ->icon('heroicon-o-book-open'),
                                            ]),
                                    ]),
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.signature_and_payment.title'))
                                            ->schema([
                                                IconEntry::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-signature'))
                                                    ->boolean(),
                                                IconEntry::make('require_payment')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-payment'))
                                                    ->boolean(),
                                                TextEntry::make('prepayment_percentage')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.prepayment-percentage'))
                                                    ->suffix('%')
                                                    ->visible(fn ($record) => $record->require_payment === true),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
