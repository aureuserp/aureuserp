<?php

namespace Webkul\Website\Filament\Admin\Resources\Page\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Webkul\Website\Models\Page as PageModel;

class PageForm
{
    public static function configure(Schema $schema): Schema
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
}
