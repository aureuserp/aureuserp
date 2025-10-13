<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ListTaxes;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ViewTax;
use Webkul\Account\Models\Tax;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.name'))
                                    ->required(),
                                Select::make('type_tax_use')
                                    ->options(TypeTaxUse::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-type'))
                                    ->required(),
                                Select::make('amount_type')
                                    ->options(AmountType::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-computation'))
                                    ->required(),
                                Select::make('tax_scope')
                                    ->options(TaxScope::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-scope')),
                                Toggle::make('is_active')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.status'))
                                    ->inline(false),
                                TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.amount'))
                                    ->suffix('%')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                            ])->columns(2),
                        Fieldset::make(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.title'))
                            ->schema([
                                TextInput::make('invoice_label')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.invoice-label')),
                                Select::make('tax_group_id')
                                    ->relationship('taxGroup', 'name')
                                    ->required()
                                    ->createOptionForm(fn (Schema $schema): Schema => TaxGroupResource::form($schema))
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.tax-group')),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.country')),
                                Select::make('price_include_override')
                                    ->options(TaxIncludeOverride::class)
                                    ->default(TaxIncludeOverride::DEFAULT->value)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-in-price'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('Overrides the Company\'s default on whether the price you use on the product and invoices includes this tax.')),
                                Toggle::make('include_base_amount')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-base-amount'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a higher sequence than this one will be affected by it, provided they accept it.')),
                                Toggle::make('is_base_affected')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.is-base-affected'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a lower sequence might affect this one, provided they try to do it.')),
                            ]),
                        RichEditor::make('description')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.description')),
                        RichEditor::make('invoice_legal_notes')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.legal-notes')),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.company'))
                    ->sortable(),
                TextColumn::make('taxGroup.name')
                    ->label(__('Tax Group'))
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.country'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-type'))
                    ->formatStateUsing(fn ($state) => TypeTaxUse::options()[$state])
                    ->sortable(),
                TextColumn::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-scope'))
                    ->formatStateUsing(fn ($state) => TaxScope::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount-type'))
                    ->formatStateUsing(fn ($state) => AmountType::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_label')
                    ->label(__('accounts::filament/resources/tax.table.columns.invoice-label'))
                    ->sortable(),
                TextColumn::make('tax_exigibility')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-exigibility'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price_include_override')
                    ->label(__('accounts::filament/resources/tax.table.columns.price-include-override'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.status'))
                    ->sortable(),
                IconColumn::make('include_base_amount')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.include-base-amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_base_affected')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.is-base-affected'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/tax.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('taxGroup.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-group'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.groups.type-tax-use'))
                    ->collapsible(),
                Tables\Grouping\Group::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-scope'))
                    ->collapsible(),
                Tables\Grouping\Group::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.groups.amount-type'))
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make()
                        ->action(function (Tax $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.name'))
                                            ->placeholder('—'),
                                        TextEntry::make('type_tax_use')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-type'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount_type')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-computation'))
                                            ->placeholder('—'),
                                        TextEntry::make('tax_scope')
                                            ->icon('heroicon-o-globe-alt')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-scope'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.amount'))
                                            ->suffix('%')
                                            ->placeholder('—'),
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.status')),
                                    ])->columns(2),
                                Section::make()
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.description'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('invoice_legal_notes')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.legal-notes'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('invoice_label')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.invoice-label'))
                                        ->placeholder('—'),
                                    TextEntry::make('taxGroup.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.tax-group'))
                                        ->placeholder('—'),
                                    TextEntry::make('country.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.country'))
                                        ->placeholder('—'),
                                    TextEntry::make('price_include_override')
                                        ->formatStateUsing(fn ($state) => TaxIncludeOverride::options()[$state] ?? $state)
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-in-price')),
                                    IconEntry::make('include_base_amount')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-base-amount')),
                                    IconEntry::make('is_base_affected')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.is-base-affected')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'view'   => ViewTax::route('/{record}'),
            'edit'   => EditTax::route('/{record}/edit'),
        ];
    }
}
