<?php

namespace Webkul\Website\Filament\Admin\Resources\Page\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PageInfolist
{
    public static function configure($schema)
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
}
