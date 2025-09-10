<?php

namespace Webkul\Blog\Filament\Admin\Resources\Post\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Webkul\Blog\Models\Post;

class PostForm
{
    public static function configure(Schema $schema): Schema
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
                                    ->relationship('category', 'name')
                                    ->searchable()
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
}
