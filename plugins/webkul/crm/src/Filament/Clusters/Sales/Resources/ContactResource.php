<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Contact\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\CreateContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\EditContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ListContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageAddresses;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageBankAccounts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ViewContact;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Crm\Policies\ContactPolicy;
use Webkul\PluginManager\Package;

class ContactResource extends BasePartnerResource
{
    use HasCrmAuthorization;

    protected static ?string $slug = 'contacts';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static bool $isGloballySearchable = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getCrmPolicy(): string
    {
        return ContactPolicy::class;
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/contact.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/contact.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/contact.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/partner-level.contact.'.$key);

        return $schema->components([
            ...$schema->getComponents(withHidden: true),
            Section::make($label('section'))
                ->relationship('crmPartner')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('level_id')
                        ->label($label('level'))
                        ->options(fn (): array => PartnerLevel::query()->orderBy('sort')->pluck('name', 'id')->all())
                        ->searchable()
                        ->live(),
                    TextInput::make('level_weight')
                        ->label($label('weight'))
                        ->helperText($label('weight-helper'))
                        ->placeholder(fn (Get $get): ?string => filled($get('level_id'))
                            ? (string) PartnerLevel::query()->whereKey($get('level_id'))->value('partner_weight')
                            : null)
                        ->numeric()
                        ->integer()
                        ->minValue(0),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = parent::infolist($schema);

        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/partner-level.contact.'.$key);

        return $schema->components([
            ...$schema->getComponents(withHidden: true),
            Section::make($label('section'))
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('crmPartner.level.name')
                        ->label($label('level'))
                        ->placeholder('—'),
                    TextEntry::make('level_weight')
                        ->label($label('weight'))
                        ->state(fn ($record): ?int => $record->crmPartner?->level_id || $record->crmPartner?->level_weight !== null
                            ? $record->crmPartner->effectiveWeight()
                            : null)
                        ->placeholder('—'),
                ]),
        ]);
    }

    public static function getNavigationItems(): array
    {
        return array_map(
            fn ($item) => $item->group(null),
            parent::getNavigationItems(),
        );
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewContact::class,
            EditContact::class,
            ManageContacts::class,
            ManageAddresses::class,
        ];

        if (Package::isPluginInstalled('accounts')) {
            $items[] = ManageBankAccounts::class;
        }

        return $page->generateNavigationItems($items);
    }

    public static function getPages(): array
    {
        $pages = [
            'index'     => ListContacts::route('/'),
            'create'    => CreateContact::route('/create'),
            'view'      => ViewContact::route('/{record}'),
            'edit'      => EditContact::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
        ];

        if (Package::isPluginInstalled('accounts')) {
            $pages['bank-accounts'] = ManageBankAccounts::route('/{record}/bank-accounts');
        }

        return $pages;
    }
}
