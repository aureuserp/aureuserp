<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Rules\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Routes\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Routes\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Models\OperationType;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;

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
                                                    ->default(RuleAction::PULL->value)
                                                    ->selectablePlaceholder(false)
                                                    ->live(),
                                                Select::make('operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'))
                                                    ->relationship('operationType', 'name')
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
                                                    ->relationship('sourceLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Select::make('destination_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'))
                                                    ->relationship('destinationLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                Placeholder::make('')
                                                    ->hiddenLabel()
                                                    ->content(new HtmlString('When products are needed in Destination Location, </br>Operation Type are created from Source Location to fulfill the need.'))
                                                    ->content(function (Get $get): HtmlString {
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

                                                        return match ($get('action') ?? RuleAction::PULL->value) {
                                                            RuleAction::PULL->value      => new HtmlString($pullMessage),
                                                            RuleAction::PUSH->value      => new HtmlString($pushMessage),
                                                            RuleAction::PULL_PUSH->value => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
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
                                    ->relationship('partnerAddress', 'name')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address-hint-tooltip')))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                    ->hidden(fn (Get $get): bool => $get('action') == RuleAction::PUSH->value),
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
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
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
                                            ->preload(),
                                    ])
                                    ->columns(1),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
