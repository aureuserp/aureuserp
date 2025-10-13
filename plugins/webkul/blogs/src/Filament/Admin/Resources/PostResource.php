<?php

namespace Webkul\Blog\Filament\Admin\Resources;

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
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
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
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\CreatePost;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\EditPost;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\ListPosts;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\ViewPost;
use Webkul\Blog\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'website/posts';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('blogs::filament/admin/resources/post.form.sections.general.title'))
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder(__('blogs::filament/admin/resources/post.form.sections.general.fields.title-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Post::class, 'slug', ignoreRecord: true),
                                Textarea::make('sub_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.sub-title')),
                                RichEditor::make('content')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.content'))
                                    ->required(),
                                FileUpload::make('image')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.banner'))
                                    ->image(),
                            ]),

                        Section::make(__('blogs::filament/admin/resources/post.form.sections.seo.title'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-title'))
                                    ->maxLength(255),
                                TextInput::make('meta_keywords')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-keywords'))
                                    ->maxLength(255),
                                Textarea::make('meta_description')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-description')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('blogs::filament/admin/resources/post.form.sections.settings.title'))
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.category'))
                                    ->relationship(
                                        name: 'category',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->withTrashed(),
                                    )
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->preload()
                                    ->required(),
                                Select::make('tags')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.tags'))
                                    ->relationship('tags', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('blogs_tags'),
                                        ColorPicker::make('color')
                                            ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.color'))
                                            ->hexColor(),
                                    ]),
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
                    ->label(__('blogs::filament/admin/resources/post.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.slug'))
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.author'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.category'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.is-published'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.created-at'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('category.name')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.category')),
                Tables\Grouping\Group::make('author.name')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.author')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Filter::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.is-published')),
                SelectFilter::make('author_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.author'))
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tags')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.tags'))
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->multiple()
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
                                ->title(__('blogs::filament/admin/resources/post.table.actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.body')),
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
                        Section::make(__('blogs::filament/admin/resources/post.form.sections.general.title'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.title'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('content')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.content'))
                                    ->markdown(),

                                ImageEntry::make('image')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.banner')),
                            ]),

                        Section::make(__('blogs::filament/admin/resources/post.form.sections.seo.title'))
                            ->schema([
                                TextEntry::make('meta_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-title'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),

                                TextEntry::make('meta_keywords')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-keywords'))
                                    ->icon('heroicon-o-hashtag')
                                    ->placeholder('—'),

                                TextEntry::make('meta_description')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-description'))
                                    ->markdown()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('blogs::filament/admin/resources/post.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('author.name')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.author'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('creator.name')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('published_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.published-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Section::make(__('blogs::filament/admin/resources/post.form.sections.settings.title'))
                            ->schema([
                                IconEntry::make('is_published')
                                    ->label(__('blogs::filament/admin/resources/post.table.columns.is-published'))
                                    ->boolean(),

                                TextEntry::make('category.name')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.category'))
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->badge()
                                    ->color('warning'),

                                TextEntry::make('tags.name')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.tags'))
                                    ->separator(', ')
                                    ->icon('heroicon-o-tag')
                                    ->badge()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPost::class,
            EditPost::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view'   => ViewPost::route('/{record}'),
            'edit'   => EditPost::route('/{record}/edit'),
        ];
    }
}
