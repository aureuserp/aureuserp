<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Models\PartnerLevel;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make($schema->getComponents(withHidden: true))
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make(__('crm::filament/clusters/configurations/resources/partner-level.contact.section'))
                        ->relationship('crmPartner')
                        ->schema([
                            Select::make('level_id')
                                ->label(__('crm::filament/clusters/configurations/resources/partner-level.contact.level'))
                                ->relationship('level', 'name', fn (Builder $query): Builder => $query->withoutTrashed()->orderBy('sort'))
                                ->searchable()
                                ->preload()
                                ->live(),
                            TextInput::make('level_weight')
                                ->label(__('crm::filament/clusters/configurations/resources/partner-level.contact.weight'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/partner-level.contact.weight-helper'))
                                ->placeholder(fn (Get $get): ?string => filled($get('level_id'))
                                    ? (string) PartnerLevel::query()->whereKey($get('level_id'))->value('partner_weight')
                                    : null)
                                ->numeric()
                                ->integer()
                                ->minValue(0),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
