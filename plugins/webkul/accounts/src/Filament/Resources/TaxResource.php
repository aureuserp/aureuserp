<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Enums;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Filament\Resources\TaxResource\Pages;
use Webkul\Account\Models\Tax;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.name'))
                                    ->required(),
                                Forms\Components\Select::make('type_tax_use')
                                    ->options(Enums\TypeTaxUse::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-type'))
                                    ->required(),
                                Forms\Components\Select::make('amount_type')
                                    ->options(Enums\AmountType::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-computation'))
                                    ->required(),
                                Forms\Components\Select::make('tax_scope')
                                    ->options(Enums\TaxScope::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-scope')),
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.status'))
                                    ->inline(false),
                                Forms\Components\TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.amount'))
                                    ->suffix('%')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                            ])->columns(2),
                        Forms\Components\Fieldset::make(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.title'))
                            ->schema([
                                Forms\Components\TextInput::make('invoice_label')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.invoice-label')),
                                Forms\Components\Select::make('tax_group_id')
                                    ->relationship('taxGroup', 'name')
                                    ->required()
                                    ->createOptionForm(fn (Form $form): Form => TaxGroupResource::form($form))
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.tax-group')),
                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.country')),
                                Forms\Components\Select::make('price_include_override')
                                    ->options(TaxIncludeOverride::class)
                                    ->default(TaxIncludeOverride::DEFAULT->value)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-in-price'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('Overrides the Company\'s default on whether the price you use on the product and invoices includes this tax.')),
                                Forms\Components\Toggle::make('include_base_amount')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-base-amount'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a higher sequence than this one will be affected by it, provided they accept it.')),
                                Forms\Components\Toggle::make('is_base_affected')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.is-base-affected'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a lower sequence might affect this one, provided they try to do it.')),
                            ]),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.description')),
                        Forms\Components\RichEditor::make('invoice_legal_notes')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.legal-notes')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.company'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxGroup.name')
                    ->label(__('Tax Group'))
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-group'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.country'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-type'))
                    ->formatStateUsing(fn ($state) => Enums\TypeTaxUse::options()[$state])
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-scope'))
                    ->formatStateUsing(fn ($state) => Enums\TaxScope::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount-type'))
                    ->formatStateUsing(fn ($state) => Enums\AmountType::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('invoice_label')
                    ->label(__('accounts::filament/resources/tax.table.columns.invoice-label'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_exigibility')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-exigibility'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price_include_override')
                    ->label(__('accounts::filament/resources/tax.table.columns.price-include-override'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.status'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('include_base_amount')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.include-base-amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_base_affected')
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.name'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('type_tax_use')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-type'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('amount_type')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-computation'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('tax_scope')
                                            ->icon('heroicon-o-globe-alt')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-scope'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('amount')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.amount'))
                                            ->suffix('%')
                                            ->placeholder('—'),
                                        Infolists\Components\IconEntry::make('is_active')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.status')),
                                    ])->columns(2),
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('description')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.description'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('invoice_legal_notes')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.legal-notes'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make()
                                ->schema([
                                    Infolists\Components\TextEntry::make('invoice_label')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.invoice-label'))
                                        ->placeholder('—'),
                                    Infolists\Components\TextEntry::make('taxGroup.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.tax-group'))
                                        ->placeholder('—'),
                                    Infolists\Components\TextEntry::make('country.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.country'))
                                        ->placeholder('—'),
                                    Infolists\Components\IconEntry::make('price_include_override')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-in-price')),
                                    Infolists\Components\IconEntry::make('include_base_amount')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-base-amount')),
                                    Infolists\Components\IconEntry::make('is_base_affected')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.is-base-affected')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'view'   => Pages\ViewTax::route('/{record}'),
            'edit'   => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
