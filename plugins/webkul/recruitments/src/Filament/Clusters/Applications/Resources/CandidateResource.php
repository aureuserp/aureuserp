<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\CreateCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\EditCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ListCandidates;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ManageSkill;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ViewCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\RelationManagers\SkillsRelationManager;
use Webkul\Recruitment\Models\Candidate;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/candidate.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/candidate.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email_from',
            'phone',
            'company.name',
            'degree.name',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.title'))
                            ->schema([
                                Hidden::make('creator_id')
                                    ->default(Auth::id()),
                                TextInput::make('name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.full-name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email_from')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.email'))
                                    ->email()
                                    ->live()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('linkedin_profile')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.linkedin'))
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.title'))
                            ->schema([
                                Select::make('degree_id')
                                    ->relationship('degree', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.degree')),
                                Select::make('recruitments_candidate_categories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.tags')),
                                Select::make('manager_id')
                                    ->relationship('manager', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.manager')),
                                DatePicker::make('availability_date')
                                    ->native(false)
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.availability-date')),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.status-and-evaluation.title'))
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('Status'))
                                    ->inline(false)
                                    ->default(true),
                                Placeholder::make('evaluation')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.status-and-evaluation.fields.evaluation'))
                                    ->content(function ($record) {
                                        $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';

                                        for ($i = 1; $i <= 3; $i++) {
                                            $iconType = $i <= $record?->priority ? 'heroicon-s-star' : 'heroicon-o-star';
                                            $html .= view('filament::components.icon', [
                                                'icon'  => $iconType,
                                                'class' => 'w-5 h-5',
                                            ])->render();
                                        }

                                        $html .= '</div>';

                                        return new HtmlString($html);
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('categories.name')
                                ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.tags'))
                                ->badge()
                                ->searchable()
                                ->weight(FontWeight::Bold)
                                ->state(function (Candidate $record): array {
                                    return $record->categories->map(fn ($category) => [
                                        'label' => $category->name,
                                        'color' => $category->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::generateV3Palette($state['color'])),
                            TextColumn::make('priority')
                                ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.evaluation'))
                                ->color('warning')
                                ->formatStateUsing(function ($state) {
                                    $html = '<div class="flex gap-1" style="margin-top: 6px;">';
                                    for ($i = 1; $i <= 3; $i++) {
                                        $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                                        $html .= view('filament::components.icon', [
                                            'icon'  => $iconType,
                                            'class' => 'w-5 h-5',
                                        ])->render();
                                    }
                                    $html .= '</div>';

                                    return new HtmlString($html);
                                }),
                        ])
                            ->visible(fn ($record) => filled($record?->categories?->count())),
                    ])->space(1),
                ])
                    ->space(4),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(1)
                    ->constraints([
                        RelationshipConstraint::make('company')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.partner-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('degree')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.degree'))
                            ->icon('heroicon-o-academic-cap')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('manager')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.manager-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('manager.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.groups.manager-name'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.empty-state-actions.create.notification.title'))
                            ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.empty-state-actions.create.notification.body'))
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.full-name')),
                                        TextEntry::make('partner.name')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.contact')),
                                        TextEntry::make('email_from')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.email')),
                                        TextEntry::make('phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.phone')),
                                        TextEntry::make('linkedin_profile')
                                            ->icon('heroicon-o-link')
                                            ->placeholder('—')
                                            ->url(fn ($record) => $record->linkedin_profile)
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.linkedin')),
                                    ])
                                    ->columns(2),
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.company')),
                                        TextEntry::make('degree.name')
                                            ->icon('heroicon-o-academic-cap')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.degree')),
                                        TextEntry::make('categories.name')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->state(function (Candidate $record): array {
                                                return $record->categories->map(fn ($category) => [
                                                    'label' => $category->name,
                                                    'color' => $category->color ?? '#808080',
                                                ])->toArray();
                                            })
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state['label'])
                                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                            ->listWithLineBreaks()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.tags')),
                                        TextEntry::make('manager.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.manager')),
                                        TextEntry::make('availability_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.availability-date')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.status-and-evaluation.title'))
                                    ->schema([
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('Status')),
                                        TextEntry::make('priority')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.status-and-evaluation.entries.evaluation'))
                                            ->formatStateUsing(function ($state) {
                                                $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';
                                                for ($i = 1; $i <= 3; $i++) {
                                                    $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                                                    $html .= view('filament::components.icon', [
                                                        'icon'  => $iconType,
                                                        'class' => 'w-5 h-5',
                                                    ])->render();
                                                }

                                                $html .= '</div>';

                                                return new HtmlString($html);
                                            })
                                            ->placeholder('—'),
                                    ]),
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.title'))
                                    ->schema([
                                        TextEntry::make('email_cc')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.entries.cc-email')),
                                        IconEntry::make('message_bounced')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.entries.email-bounced')),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCandidate::class,
            EditCandidate::class,
            ManageSkill::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Manage Skills', [
                SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCandidates::route('/'),
            'create' => CreateCandidate::route('/create'),
            'edit'   => EditCandidate::route('/{record}/edit'),
            'view'   => ViewCandidate::route('/{record}'),
            'skills' => ManageSkill::route('/{record}/skills'),
        ];
    }
}
