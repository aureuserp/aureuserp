<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Enums\OrderDisplayType;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages;
use Webkul\Sale\Models\OrderTemplate;

class QuotationTemplateResource extends Resource
{
    protected static ?string $model = OrderTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'company.name',
            'name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('sales::filament/clusters/configurations/resources/quotation-template.global-search.company') => $record->company?->name ?? '—',
            __('sales::filament/clusters/configurations/resources/quotation-template.global-search.name')    => $record->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Tabs::make()
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.title'))
                                            ->schema([
                                                static::getProductRepeater(),
                                                static::getSectionRepeater(),
                                                static::getNoteRepeater(),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                Forms\Components\RichEditor::make('note')
                                                    ->placeholder(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.note-placeholder'))
                                                    ->hiddenLabel(),
                                            ]),
                                    ])
                                    ->persistTabInQueryString(),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.title'))
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.name'))
                                                    ->required(),
                                                Forms\Components\TextInput::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.quotation-validity'))
                                                    ->default(0)
                                                    ->required(),
                                                Forms\Components\Select::make('journal_id')
                                                    ->relationship('journal', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.sale-journal'))
                                                    ->required(),
                                            ])->columns(1),
                                    ]),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.title'))
                                            ->schema([
                                                Forms\Components\Toggle::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-signature')),
                                                Forms\Components\Toggle::make('require_payment')
                                                    ->live()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-payment')),
                                                Forms\Components\TextInput::make('prepayment_percentage')
                                                    ->prefix('of')
                                                    ->suffix('%')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.prepayment-percentage'))
                                                    ->visible(fn(Get $get) => $get('require_payment') === true),
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
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.created-by'))
                    ->label(__('Created By')),
                Tables\Columns\TextColumn::make('company.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.company')),
                Tables\Columns\TextColumn::make('name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.name')),
                Tables\Columns\TextColumn::make('number_of_days')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.number-of-days')),
                Tables\Columns\TextColumn::make('journal.name')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.journal')),
                Tables\Columns\IconColumn::make('require_signature')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.signature-required')),
                Tables\Columns\IconColumn::make('require_payment')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.payment-required')),
                Tables\Columns\TextColumn::make('prepayment_percentage')
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.prepayment-percentage')),
            ])
            ->filtersFormColumns(2)
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('createdBy.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('name')
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index'  => Pages\ListQuotationTemplates::route('/'),
            'create' => Pages\CreateQuotationTemplate::route('/create'),
            'view'   => Pages\ViewQuotationTemplate::route('/{record}'),
            'edit'   => Pages\EditQuotationTemplate::route('/{record}/edit'),
        ];
    }

    public static function getProductRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('products')
            ->relationship('products')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn(Action $action) => $action->requiresConfirmation(),
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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.products'))
                                    ->label('Product')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->live(onBlur: true)
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name'))
                                    ->label('Name'),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity')),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function getSectionRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('sections')
            ->relationship('sections')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn(Action $action) => $action->requiresConfirmation(),
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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->live(onBlur: true)
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name')),
                        Forms\Components\Hidden::make('quantity')
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity'))
                            ->default(0),
                        Forms\Components\Hidden::make('display_type')
                            ->required()
                            ->default(OrderDisplayType::SECTION->value),
                    ]),
            ]);
    }

    public static function getNoteRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('notes')
            ->relationship('notes')
            ->hiddenLabel()
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
            ->deleteAction(
                fn(Action $action) => $action->requiresConfirmation(),
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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->live(onBlur: true)
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.name')),
                        Forms\Components\Hidden::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.fields.quantity'))
                            ->required()
                            ->default(0),
                        Forms\Components\Hidden::make('display_type')
                            ->required()
                            ->default(OrderDisplayType::NOTE->value),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Tabs::make('Tabs')
                                    ->tabs([
                                        Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.products.title'))
                                            ->schema([
                                                Infolists\Components\RepeatableEntry::make('products')
                                                    ->hiddenLabel()
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('product.name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.product'))
                                                            ->icon('heroicon-o-shopping-bag'),
                                                        Infolists\Components\TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                        Infolists\Components\TextEntry::make('quantity')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quantity'))
                                                            ->numeric(),
                                                        Infolists\Components\TextEntry::make('unit-price')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.unit-price'))
                                                            ->money('USD'),
                                                    ])
                                                    ->columns(4),

                                                Infolists\Components\RepeatableEntry::make('sections')
                                                    ->hiddenLabel()
                                                    ->hidden(fn($record) => $record->sections->isEmpty())
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.section-name')),
                                                        Infolists\Components\TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),

                                                Infolists\Components\RepeatableEntry::make('notes')
                                                    ->hiddenLabel()
                                                    ->hidden(fn($record) => $record->notes->isEmpty())
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.note-title')),
                                                        Infolists\Components\TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),
                                            ]),
                                        Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('note')
                                                    ->markdown()
                                                    ->hiddenLabel()
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->persistTabInQueryString(),
                            ])->columnSpan(['lg' => 2]),
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.general.title'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.name'))
                                                    ->icon('heroicon-o-document-text'),
                                                Infolists\Components\TextEntry::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quotation-validity'))
                                                    ->suffix(' days')
                                                    ->icon('heroicon-o-calendar'),
                                                Infolists\Components\TextEntry::make('journal.name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.sale-journal'))
                                                    ->icon('heroicon-o-book-open'),
                                            ]),
                                    ]),
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.signature_and_payment.title'))
                                            ->schema([
                                                Infolists\Components\IconEntry::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-signature'))
                                                    ->boolean(),
                                                Infolists\Components\IconEntry::make('require_payment')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-payment'))
                                                    ->boolean(),
                                                Infolists\Components\TextEntry::make('prepayment_percentage')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.prepayment-percentage'))
                                                    ->suffix('%')
                                                    ->visible(fn($record) => $record->require_payment === true),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
