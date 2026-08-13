<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                        ->placeholder(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.name-placeholder')),
                    Select::make('color')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.color'))
                        ->options(function () {
                            return collect(Colors::options())->mapWithKeys(function ($value, $key) {
                                return [
                                    $key => '<div class="flex items-center gap-4"><span class="flex h-5 w-5 rounded-full" style="background: var(--'.$key.'-500)"></span> '.$value.'</div>',
                                ];
                            });
                        })
                        ->native(false)
                        ->allowHtml(),
                    Toggle::make('is_active')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.status'))
                        ->default(true),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
