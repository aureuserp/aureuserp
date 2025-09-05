<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\Colors;

class SkillTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('Enter skill type name'),
                    Hidden::make('creator_id')
                        ->default(Auth::user()->id),
                    Select::make('color')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.color'))
                        ->options(function () {
                            return collect(Colors::options())->mapWithKeys(function ($value, $key) {
                                return [
                                    $key => '<div class="flex items-center gap-4"><span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$key.'-500))"></span> '.$value.'</span>',
                                ];
                            });
                        })
                        ->native(false)
                        ->allowHtml(),
                    Toggle::make('is_active')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.status'))
                        ->default(true),
                ])->columns(2),
            ]);
    }
}
