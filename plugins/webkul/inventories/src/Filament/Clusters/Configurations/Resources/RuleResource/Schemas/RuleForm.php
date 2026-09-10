<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;

class RuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action'))
                                                    ->required()
                                                    ->options(RuleAction::class)
                                                    ->disableOptionWhen(fn (string $value): bool => $value === RuleAction::MANUFACTURE->value && ! Package::isPluginInstalled('manufacturing'))
                                                    ->default(RuleAction::PULL)
                                                    ->selectablePlaceholder(false)
                                                    ->live(),
                                                Select::make('operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'))
                                                    ->relationship(
                                                        'operationType',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                                        if (! $record->warehouse) {
                                                            return $record->name;
                                                        }

                                                        return $record->warehouse->name.': '.$record->name;
                                                    })
                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                        $operationType = OperationType::find($get('operation_type_id'));

                                                        $set('source_location_id', $operationType?->source_location_id);

                                                        $set('destination_location_id', $operationType?->destination_location_id);
                                                    })
                                                    ->live(),
                                                Select::make('source_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'))
                                                    ->relationship(
                                                        'sourceLocation',
                                                        'full_name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Select::make('destination_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'))
                                                    ->relationship(
                                                        'destinationLocation',
                                                        'full_name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Select::make('procure_method')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.supply-method'))
                                                    ->required()
                                                    ->options(ProcureMethod::class)
                                                    ->selectablePlaceholder(false)
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.supply-method-hint-tooltip')))
                                                    ->hidden(fn (Get $get): bool => $get('action') == RuleAction::PUSH),
                                                Select::make('auto')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.automatic-move'))
                                                    ->required()
                                                    ->options(RuleAuto::class)
                                                    ->selectablePlaceholder(false)
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.automatic-move-hint-tooltip')))
                                                    ->hidden(fn (Get $get): bool => $get('action') == RuleAction::PULL),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('placeholder')
                                                    ->hiddenLabel()
                                                    ->getStateUsing(function (Get $get): HtmlString {
                                                        $operation = OperationType::find($get('operation_type_id'));

                                                        $pullMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.pull', [
                                                            'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                            'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'),
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'),
                                                        ]);

                                                        $pushMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.push', [
                                                            'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'),
                                                            'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'),
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                        ]);

                                                        $buyMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.buy', [
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                        ]);

                                                        $manufactureMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.manufacture', [
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                        ]);

                                                        $action = ($get('action') instanceof RuleAction)
                                                            ? $get('action')
                                                            : RuleAction::tryFrom($get('action')?->value ?? RuleAction::PULL->value);

                                                        return match ($action) {
                                                            RuleAction::PULL        => new HtmlString($pullMessage),
                                                            RuleAction::PUSH        => new HtmlString($pushMessage),
                                                            RuleAction::PULL_PUSH   => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                                            RuleAction::BUY         => new HtmlString($buyMessage),
                                                            RuleAction::MANUFACTURE => new HtmlString($manufactureMessage),
                                                        };
                                                    }),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.title'))
                            ->schema([
                                Select::make('partner_address_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address'))
                                    ->relationship(
                                        'partnerAddress',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address-hint-tooltip')))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                    ->hidden(fn (Get $get): bool => $get('action') == RuleAction::PUSH),
                                TextInput::make('delay')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time-hint-tooltip')))
                                    ->integer()
                                    ->minValue(0),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.title'))
                                    ->schema([
                                        Select::make('route_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.fields.route'))
                                            ->relationship(
                                                'route',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->withTrashed()
                                                    ->where(owned_by_company($get('company_id'))),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->hiddenOn([ManageRules::class, RulesRelationManager::class]),
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(current_company_id())
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                                'operation_type_id'       => OperationType::class,
                                                'source_location_id'      => Location::class,
                                                'destination_location_id' => Location::class,
                                                'route_id'                => Route::class,
                                                'partner_address_id'      => Partner::class,
                                            ], $state)),
                                    ])
                                    ->columns(1),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
