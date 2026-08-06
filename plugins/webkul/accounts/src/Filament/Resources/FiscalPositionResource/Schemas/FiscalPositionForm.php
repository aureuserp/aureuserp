<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Tax;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class FiscalPositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.name'))
                                    ->required()
                                    ->placeholder(__('Name')),
                                TextInput::make('foreign_vat')
                                    ->label(__('Foreign VAT'))
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.foreign-vat'))
                                    ->required(),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country')),
                                Select::make('country_group_id')
                                    ->relationship('countryGroup', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country-group')),
                                TextInput::make('zip_from')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-from'))
                                    ->required(),
                                TextInput::make('zip_to')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-to'))
                                    ->required(),
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.company'))
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                        $mappings = [
                                            'taxes'    => [Tax::class, ['tax_source_id', 'tax_destination_id']],
                                            'accounts' => [Account::class, ['account_source_id', 'account_destination_id']],
                                        ];

                                        foreach ($mappings as $repeater => [$model, $mappedFields]) {
                                            $rows = $get($repeater);

                                            if (! is_array($rows)) {
                                                continue;
                                            }

                                            $referenced = collect($rows)
                                                ->flatMap(fn ($row) => array_map(fn ($field) => $row[$field] ?? null, $mappedFields))
                                                ->filter()
                                                ->unique()
                                                ->all();

                                            if (empty($referenced)) {
                                                continue;
                                            }

                                            $allowed = $model::query()
                                                ->withoutGlobalScopes()
                                                ->whereKey($referenced)
                                                ->where(owned_by_company($state))
                                                ->pluck('id')
                                                ->all();

                                            foreach ($rows as $key => $row) {
                                                foreach ($mappedFields as $field) {
                                                    $recordId = $row[$field] ?? null;

                                                    if (filled($recordId) && ! in_array($recordId, $allowed)) {
                                                        $rows[$key][$field] = null;
                                                    }
                                                }
                                            }

                                            $set($repeater, $rows);
                                        }
                                    })
                                    ->required(),

                                Toggle::make('auto_reply')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.detect-automatically')),
                            ])->columns(2),
                        RichEditor::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.form.fields.notes')),
                    ])->columnSpanFull(),
                Tabs::make('Mappings')
                    ->tabs([
                        Tab::make('Tax Mapping')
                            ->schema([
                                Repeater::make('taxes')
                                    ->hiddenLabel()
                                    ->relationship('taxes')
                                    ->compact()
                                    ->reactive()
                                    ->addActionLabel(__('Add Tax Mapping'))
                                    ->table([
                                        TableColumn::make('tax_source_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-source'))
                                            ->resizable(),

                                        TableColumn::make('tax_destination_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-destination'))
                                            ->resizable(),
                                    ])
                                    ->schema([
                                        Select::make('tax_source_id')
                                            ->relationship(
                                                'taxSource',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                                            )
                                            ->wrapOptionLabels(false)
                                            ->label(__('accounts::traits/fiscal-position-tax.form.fields.tax-source'))
                                            ->preload()
                                            ->searchable()
                                            ->required(),

                                        Select::make('tax_destination_id')
                                            ->relationship(
                                                'taxDestination',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                                            )
                                            ->wrapOptionLabels(false)
                                            ->label(__('accounts::traits/fiscal-position-tax.form.fields.tax-destination'))
                                            ->preload()
                                            ->searchable(),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Account Mapping')
                            ->schema([
                                Repeater::make('accounts')
                                    ->hiddenLabel()
                                    ->relationship('accounts')
                                    ->compact()
                                    ->reactive()
                                    ->addActionLabel(__('Add Account Mapping'))
                                    ->table([
                                        TableColumn::make('account_source_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.source-account'))
                                            ->resizable()
                                            ->wrapHeader(false),
                                        TableColumn::make('account_destination_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.destination-account'))
                                            ->resizable()
                                            ->wrapHeader(false),
                                    ])
                                    ->schema([
                                        Select::make('account_source_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.source-account'))
                                            ->wrapOptionLabels(false)
                                            ->searchable()
                                            ->preload()
                                            ->relationship(
                                                'accountSource',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                                            )
                                            ->required(),

                                        Select::make('account_destination_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.destination-account'))
                                            ->wrapOptionLabels(false)
                                            ->searchable()
                                            ->preload()
                                            ->relationship(
                                                'accountDestination',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                                            )
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])->columnSpanFull(),

            ]);
    }
}
