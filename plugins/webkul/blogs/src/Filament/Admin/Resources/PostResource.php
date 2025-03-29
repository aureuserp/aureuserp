<?php

namespace Webkul\Blog\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;
use Webkul\Blog\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'website/posts';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.general.title'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder(__('blogs::filament/admin/resources/post.form.sections.general.fields.title-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Post::class, 'slug', ignoreRecord: true),
                                Forms\Components\Textarea::make('sub_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.sub-title')),
                                Forms\Components\RichEditor::make('content')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.content'))
                                    ->required(),
                                Forms\Components\FileUpload::make('image')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.banner'))
                                    ->image(),
                            ]),

                        Forms\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.seo.title'))
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-title')),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-keywords')),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-description')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.settings.title'))
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.category'))
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('tags')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.tags'))
                                    ->relationship('tags', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.name'))
                                            ->required()
                                            ->unique('blogs_tags'),
                                        Forms\Components\ColorPicker::make('color')
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
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.slug'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.author'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.category'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.is-published'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Filters\Filter::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.is-published')),
                Tables\Filters\SelectFilter::make('author_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.author'))
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('tags')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.tags'))
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.title'))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                Infolists\Components\TextEntry::make('content')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.content'))
                                    ->markdown(),

                                Infolists\Components\ImageEntry::make('image')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.general.fields.banner')),
                            ]),

                        Infolists\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.seo.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('meta_title')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-title'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('meta_keywords')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-keywords'))
                                    ->icon('heroicon-o-hashtag')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('meta_description')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.seo.fields.meta-description'))
                                    ->markdown()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('blogs::filament/admin/resources/post.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('author.name')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.author'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('published_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.published-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('blogs::filament/admin/resources/post.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Infolists\Components\Section::make(__('blogs::filament/admin/resources/post.form.sections.settings.title'))
                            ->schema([
                                Infolists\Components\IconEntry::make('is_published')
                                    ->label(__('blogs::filament/admin/resources/post.table.columns.is-published'))
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('category.name')
                                    ->label(__('blogs::filament/admin/resources/post.form.sections.settings.fields.category'))
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->badge()
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('tags.name')
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
            Pages\ViewPost::class,
            Pages\EditPost::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view'   => Pages\ViewPost::route('/{record}'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
