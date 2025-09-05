<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Webkul\Blog\Models\Category;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->placeholder(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.name-placeholder'))
                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(Category::class, 'slug', ignoreRecord: true),
                TextInput::make('sub_title')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.sub-title'))
                    ->maxLength(255),
            ])
            ->columns(1);
    }
}
