<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('partners::filament/resources/partner.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Radio::make('account_type')
                                            ->hiddenLabel()
                                            ->inline()
                                            ->columnSpan(2)
                                            ->options(AccountType::class)
                                            ->default(AccountType::INDIVIDUAL->value)
                                            ->options(function () {
                                                $options = AccountType::options();

                                                unset($options[AccountType::ADDRESS->value]);

                                                return $options;
                                            })
                                            ->live(),
                                        TextInput::make('name')
                                            ->hiddenLabel()
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2)
                                            ->placeholder(fn (Get $get): string => $get('account_type') === AccountType::INDIVIDUAL ? 'Jhon Doe' : 'ACME Corp')
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                        Select::make('parent_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.company'))
                                            ->relationship(
                                                name: 'parent',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn ($query) => $query->where(function ($q) {
                                                    $q->where('account_type', 'company')
                                                        ->orWhere('sub_type', 'company');
                                                })
                                            )
                                            ->visible(fn (Get $get): bool => $get('account_type') === AccountType::INDIVIDUAL)
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(2)
                                            ->createOptionForm(fn (Schema $schema): Schema => self::form($schema))
                                            ->editOptionForm(fn (Schema $schema): Schema => self::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                $action
                                                    ->fillForm(function (array $arguments): array {
                                                        return [
                                                            'account_type' => AccountType::COMPANY->value,
                                                        ];
                                                    })
                                                    ->mutateDataUsing(function (array $data) {
                                                        $data['account_type'] = AccountType::COMPANY->value;

                                                        return $data;
                                                    });
                                            })
                                            ->afterStateHydrated(function (Select $component, $state) {
                                                if (empty($state)) {
                                                    $component->state(null);

                                                    return;
                                                }

                                                $parent = Partner::find($state);

                                                if (! $parent) {
                                                    $component->state(null);
                                                }
                                            }),
                                    ]),
                                Group::make()
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->image()
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->imageEditor()
                                            ->avatar()
                                            ->directory('partners/avatar')
                                            ->visibility('public'),
                                    ]),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('tax_id')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.tax-id'))
                                    ->placeholder('e.g. 29ABCDE1234F1Z5')
                                    ->maxLength(255),
                                TextInput::make('job_title')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.job-title'))
                                    ->placeholder('e.g. CEO')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('mobile')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.mobile'))
                                    ->maxLength(255)
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.email'))
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('website')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.website'))
                                    ->maxLength(255)
                                    ->url(),
                                Select::make('title_id')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.title'))
                                    ->relationship('title', 'name')
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('partners_titles'),
                                        TextInput::make('short_name')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.short-name'))
                                            ->label('Short Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('partners_titles'),
                                        Hidden::make('creator_id')
                                            ->default(Auth::user()->id),
                                    ]),
                                Select::make('tags')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('partners_tags'),
                                                ColorPicker::make('color')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.color'))
                                                    ->hexColor(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Fieldset::make('Address')
                                    ->schema([
                                        TextInput::make('street1')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.street1'))
                                            ->maxLength(255),
                                        TextInput::make('street2')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.street2'))
                                            ->maxLength(255),
                                        TextInput::make('city')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.city'))
                                            ->maxLength(255),
                                        TextInput::make('zip')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.zip'))
                                            ->maxLength(255),
                                        Select::make('country_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.country'))
                                            ->relationship(name: 'country', titleAttribute: 'name')
                                            ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                            ->searchable()
                                            ->preload()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $set('state_id', null);
                                            })
                                            ->live(),
                                        Select::make('state_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.state'))
                                            ->relationship(
                                                name: 'state',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                            )
                                            ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                                                return $schema
                                                    ->components([
                                                        TextInput::make('name')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.name'))
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('code')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.code'))
                                                            ->required()
                                                            ->unique('states')
                                                            ->maxLength(255),
                                                        Select::make('country_id')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.country'))
                                                            ->relationship('country', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->default($get('country_id'))
                                                            ->afterStateUpdated(function (Get $get) use ($set) {
                                                                $set('country_id', $get('country_id'));
                                                            }),
                                                    ]);
                                            })
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),

                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('partners::filament/resources/partner.form.tabs.sales-purchase.title'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Fieldset::make('Sales')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.responsible'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('partners::filament/resources/partner.form.tabs.sales-purchase.fields.responsible-hint-text')),
                                    ])
                                    ->columns(1),

                                Fieldset::make('Others')
                                    ->schema([
                                        TextInput::make('company_registry')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.company-id'))
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('partners::filament/resources/partner.form.tabs.sales-purchase.fields.company-id-hint-text')),
                                        TextInput::make('reference')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.reference'))
                                            ->maxLength(255),
                                        Select::make('industry_id')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.industry'))
                                            ->relationship('industry', 'name'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('avatar')
                        ->height(200)
                        ->width(250)
                        ->alignCenter(),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('parent.name')
                                ->label(__('partners::filament/resources/partner.table.columns.parent'))
                                ->icon(fn (Partner $record) => $record->parent->account_type === AccountType::INDIVIDUAL->value ? 'heroicon-o-user' : 'heroicon-o-building-office')
                                ->tooltip(__('partners::filament/resources/partner.table.columns.parent'))
                                ->sortable(),
                        ])
                            ->visible(fn (Partner $record) => filled($record->parent)),
                        Stack::make([
                            TextColumn::make('job_title')
                                ->icon('heroicon-m-briefcase')
                                ->searchable()
                                ->sortable()
                                ->label('Job Title'),
                        ])
                            ->visible(fn ($record) => filled($record->job_title)),
                        Stack::make([
                            TextColumn::make('email')
                                ->icon('heroicon-o-envelope')
                                ->searchable()
                                ->sortable()
                                ->label('Work Email')
                                ->color('gray')
                                ->limit(20),
                        ])
                            ->visible(fn ($record) => filled($record->email)),
                        Stack::make([
                            TextColumn::make('phone')
                                ->icon('heroicon-o-phone')
                                ->searchable()
                                ->label('Work Phone')
                                ->color('gray')
                                ->limit(30)
                                ->sortable(),
                        ])
                            ->visible(fn ($record) => filled($record->phone)),
                        Stack::make([
                            TextColumn::make('tags.name')
                                ->badge()
                                ->state(function (Partner $record): array {
                                    return $record->tags()->get()->map(fn ($tag) => [
                                        'label' => $tag->name,
                                        'color' => $tag->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                ->weight(FontWeight::Bold),
                        ])
                            ->visible(fn ($record): bool => (bool) $record->tags?->count()),
                    ])->space(1),
                ])->space(4),
            ])
            ->groups([
                Tables\Grouping\Group::make('account_type')
                    ->label(__('partners::filament/resources/partner.table.groups.account-type')),
                Tables\Grouping\Group::make('parent.name')
                    ->label(__('partners::filament/resources/partner.table.groups.parent')),
                Tables\Grouping\Group::make('title.name')
                    ->label(__('partners::filament/resources/partner.table.groups.title')),
                Tables\Grouping\Group::make('job_title')
                    ->label(__('partners::filament/resources/partner.table.groups.job-title')),
                Tables\Grouping\Group::make('industry.name')
                    ->label(__('partners::filament/resources/partner.table.groups.industry')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        SelectConstraint::make('account_type')
                            ->label(__('partners::filament/resources/partner.table.filters.account-type'))
                            ->multiple()
                            ->options(AccountType::class)
                            ->icon('heroicon-o-bars-2'),
                        TextConstraint::make('name')
                            ->label(__('partners::filament/resources/partner.table.filters.name')),
                        TextConstraint::make('email')
                            ->label(__('partners::filament/resources/partner.table.filters.email'))
                            ->icon('heroicon-o-envelope'),
                        TextConstraint::make('job_title')
                            ->label(__('partners::filament/resources/partner.table.filters.job-title')),
                        TextConstraint::make('website')
                            ->label(__('partners::filament/resources/partner.table.filters.website'))
                            ->icon('heroicon-o-globe-alt'),
                        TextConstraint::make('tax_id')
                            ->label(__('partners::filament/resources/partner.table.filters.tax-id'))
                            ->icon('heroicon-o-identification'),
                        TextConstraint::make('phone')
                            ->label(__('partners::filament/resources/partner.table.filters.phone'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('mobile')
                            ->label(__('partners::filament/resources/partner.table.filters.mobile'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('company_registry')
                            ->label(__('partners::filament/resources/partner.table.filters.company-registry'))
                            ->icon('heroicon-o-clipboard'),
                        TextConstraint::make('reference')
                            ->label(__('partners::filament/resources/partner.table.filters.reference'))
                            ->icon('heroicon-o-hashtag'),
                        RelationshipConstraint::make('parent')
                            ->label(__('partners::filament/resources/partner.table.filters.parent'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('creator')
                            ->label(__('partners::filament/resources/partner.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('partners::filament/resources/partner.table.filters.responsible'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('title')
                            ->label(__('partners::filament/resources/partner.table.filters.title'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('partners::filament/resources/partner.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('industry')
                            ->label(__('partners::filament/resources/partner.table.filters.industry'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                    ]),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (ForceDeleteAction $action, Partner $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('partners::filament/resources/partner.table.actions.force-delete.notification.error.title'))
                                ->body(__('partners::filament/resources/partner.table.actions.force-delete.notification.error.body'))
                                ->send();
                            $action->cancel();

                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner.table.actions.force-delete.notification.success.title'))
                            ->body(__('partners::filament/resources/partner.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                                $action->cancel();

                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('partners::filament/resources/partner.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['tags'])->where('account_type', '!=', AccountType::ADDRESS);
            })
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->paginated([
                16,
                32,
                64,
                'all',
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('partners::filament/resources/partner.infolist.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextEntry::make('account_type')
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('name')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextSize::Large),

                                        TextEntry::make('parent.name')
                                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.company'))
                                            ->visible(fn ($record): bool => $record->account_type === AccountType::INDIVIDUAL->value),
                                    ]),

                                Group::make()
                                    ->schema([
                                        ImageEntry::make('avatar')
                                            ->circular()
                                            ->height(100)
                                            ->width(100),
                                    ]),
                            ])->columns(2),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('tax_id')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.tax-id'))
                                    ->placeholder('—'),

                                TextEntry::make('job_title')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.job-title'))
                                    ->placeholder('—'),

                                TextEntry::make('phone')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.phone'))
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('—'),

                                TextEntry::make('mobile')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.mobile'))
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->placeholder('—'),

                                TextEntry::make('email')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.email'))
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('website')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.website'))
                                    // ->url()
                                    ->icon('heroicon-o-globe-alt')
                                    ->placeholder('—'),

                                TextEntry::make('title.name')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.title'))
                                    ->placeholder('—'),

                                TextEntry::make('tags.name')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.tags'))
                                    ->badge()
                                    ->state(function (Partner $record): array {
                                        return $record->tags()->get()->map(fn ($tag) => [
                                            'label' => $tag->name,
                                            'color' => $tag->color ?? '#808080',
                                        ])->toArray();
                                    })
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state['label'])
                                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                    ->separator(',')
                                    ->visible(fn ($record): bool => (bool) $record->tags()->count()),
                            ]),

                        Fieldset::make('Address')
                            ->schema([
                                TextEntry::make('street1')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.street1'))
                                    ->placeholder('—'),

                                TextEntry::make('street2')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.street2'))
                                    ->placeholder('—'),

                                TextEntry::make('city')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.city'))
                                    ->placeholder('—'),

                                TextEntry::make('zip')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.zip'))
                                    ->placeholder('—'),

                                TextEntry::make('country.name')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.country'))
                                    ->placeholder('—'),

                                TextEntry::make('state.name')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.state'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.title'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Sales')
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.responsible'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(1),

                                Section::make('Others')
                                    ->schema([
                                        TextEntry::make('company_registry')
                                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.company-id'))
                                            ->placeholder('—'),

                                        TextEntry::make('reference')
                                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.reference'))
                                            ->placeholder('—'),

                                        TextEntry::make('industry.name')
                                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.industry'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(1);
    }
}
