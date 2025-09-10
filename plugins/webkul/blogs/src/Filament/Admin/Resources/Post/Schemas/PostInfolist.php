<?php

namespace Webkul\Blog\Filament\Admin\Resources\Post\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
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
}
