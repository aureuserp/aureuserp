<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Models\AssetCategory;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/category.form.sections.general.title'))
                            ->schema(static::generalComponents())
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/category.form.sections.settings.title'))
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('assets::filament/resources/category.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (): ?int => current_company_id())
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                        'parent_id' => AssetCategory::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    /**
     * Shared category fields, reused by ProductForm's inline create/edit option forms.
     *
     * @return array<int, mixed>
     */
    public static function generalComponents(): array
    {
        return [
            TextInput::make('name')
                ->label(__('assets::filament/resources/category.form.sections.general.fields.name'))
                ->required()
                ->maxLength(255)
                ->autofocus(),

            Select::make('parent_id')
                ->label(__('assets::filament/resources/category.form.sections.general.fields.parent'))
                ->relationship(
                    'parent',
                    'full_name',
                    modifyQueryUsing: fn (Builder $query, Get $get, ?Model $record) => $query
                        ->where(owned_by_company($get('company_id')))
                        ->when(
                            $record instanceof AssetCategory && $record->exists,
                            fn (Builder $q) => $q
                                ->whereKeyNot($record->getKey())
                                ->where('parent_path', 'not like', ($record->parent_path ?: '/').$record->id.'/%'),
                        )
                        ->orderBy('full_name'),
                )
                ->searchable()
                ->preload()
                ->nullable(),
        ];
    }
}
