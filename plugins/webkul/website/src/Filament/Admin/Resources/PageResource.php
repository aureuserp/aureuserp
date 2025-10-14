<?php

namespace Webkul\Website\Filament\Admin\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\CreatePage;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\EditPage;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\ListPages;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\ViewPage;
use Webkul\Website\Models\Page as PageModel;

class PageResource extends Resource
{
    protected static ?string $model = PageModel::class;

    protected static ?string $slug = 'website/pages';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/page.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('website::filament/admin/resources/page.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('website::filament/admin/resources/page.form.sections.general.title'))
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('website::filament/admin/resources/page.form.sections.general.fields.title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder(__('website::filament/admin/resources/page.form.sections.general.fields.title-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(PageModel::class, 'slug', ignoreRecord: true),
                                RichEditor::make('content')
                                    ->label(__('website::filament/admin/resources/page.form.sections.general.fields.content'))
                                    ->required(),
                            ]),

                        Section::make(__('website::filament/admin/resources/page.form.sections.seo.title'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-title')),
                                TextInput::make('meta_keywords')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-keywords')),
                                Textarea::make('meta_description')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-description')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('website::filament/admin/resources/page.form.sections.settings.title'))
                            ->schema([
                                Toggle::make('is_header_visible')
                                    ->label(__('website::filament/admin/resources/page.form.sections.settings.fields.is-header-visible'))
                                    ->inline(false),
                                Toggle::make('is_footer_visible')
                                    ->label(__('website::filament/admin/resources/page.form.sections.settings.fields.is-footer-visible'))
                                    ->inline(false),
                            ]),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('title')
                    ->label(__('website::filament/admin/resources/page.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('website::filament/admin/resources/page.table.columns.slug'))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('website::filament/admin/resources/page.table.columns.creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-published'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_header_visible')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-header-visible'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_footer_visible')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-footer-visible'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('website::filament/admin/resources/page.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('website::filament/admin/resources/page.table.columns.created-at'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label(__('website::filament/admin/resources/page.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Filter::make('is_published')
                    ->label(__('website::filament/admin/resources/page.table.filters.is-published')),
                SelectFilter::make('creator_id')
                    ->label(__('website::filament/admin/resources/page.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.actions.restore.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.actions.delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.actions.force-delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.restore.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('website::filament/admin/resources/page.form.sections.general.title'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('website::filament/admin/resources/page.form.sections.general.fields.title'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('content')
                                    ->label(__('website::filament/admin/resources/page.form.sections.general.fields.content'))
                                    ->markdown(),
                            ]),

                        Section::make(__('website::filament/admin/resources/page.form.sections.seo.title'))
                            ->schema([
                                TextEntry::make('meta_title')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-title'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),

                                TextEntry::make('meta_keywords')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-keywords'))
                                    ->icon('heroicon-o-hashtag')
                                    ->placeholder('—'),

                                TextEntry::make('meta_description')
                                    ->label(__('website::filament/admin/resources/page.form.sections.seo.fields.meta-description'))
                                    ->markdown()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('website::filament/admin/resources/page.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('published_at')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.record-information.entries.published-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),

                                IconEntry::make('is_published')
                                    ->label(__('website::filament/admin/resources/page.table.columns.is-published'))
                                    ->boolean(),

                            ]),

                        Section::make(__('website::filament/admin/resources/page.infolist.sections.settings.title'))
                            ->schema([
                                IconEntry::make('is_header_visible')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.settings.entries.is-header-visible'))
                                    ->boolean(),

                                IconEntry::make('is_footer_visible')
                                    ->label(__('website::filament/admin/resources/page.infolist.sections.settings.entries.is-footer-visible'))
                                    ->boolean(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPage::class,
            EditPage::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view'   => ViewPage::route('/{record}'),
            'edit'   => EditPage::route('/{record}/edit'),
        ];
    }
}
