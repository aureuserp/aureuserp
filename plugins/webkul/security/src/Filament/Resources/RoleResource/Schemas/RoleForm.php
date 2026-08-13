<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Schemas;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->extraAlpineAttributes([
                                // Bulk mode keeps "all" and "none" cheap by avoiding mass Livewire state writes.
                                // If the user switches back to manual edits after "all", we materialize state once.
                                'x-init' => <<<'JS'
let bulkMode = 'manual';
let updateToggleTimer = null;
const checkboxSelector = '.fi-fo-checkbox-list-option input[type=checkbox]';

const getCheckboxes = () => Array.from(document.querySelectorAll(checkboxSelector));
const setBulkMode = (mode) => {
    bulkMode = mode;
    $wire.$set('data.permissions_sync_mode', mode, false);
};
const getCheckboxModels = () => Array.from(new Set(
    getCheckboxes()
        .map((checkbox) => checkbox.getAttribute('wire:model')
            || checkbox.getAttribute('wire:model.defer')
            || checkbox.getAttribute('wire:model.live'))
        .filter(Boolean)
));

const getCheckboxGroups = () => {
    const groups = {};

    getCheckboxes().forEach((checkbox) => {
        const model = checkbox.getAttribute('wire:model')
            || checkbox.getAttribute('wire:model.defer')
            || checkbox.getAttribute('wire:model.live');

        if (! model || checkbox.disabled) {
            return;
        }

        groups[model] ??= [];

        if (checkbox.checked) {
            groups[model].push(checkbox.value);
        }
    });

    return groups;
};

const syncManualStateFromDom = () => {
    Object.entries(getCheckboxGroups()).forEach(([model, values]) => {
        $wire.$set(model, values, false);
    });
};

const updateToggleState = () => {
    clearTimeout(updateToggleTimer);

    updateToggleTimer = setTimeout(() => {
        const checkboxes = getCheckboxes().filter((checkbox) => ! checkbox.disabled);
        const areAllChecked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);

        $wire.$set('data.select_all', areAllChecked, false);
        window.dispatchEvent(new CustomEvent('shield-set-state', { detail: areAllChecked }));
    }, 40);
};

const setAllCheckboxes = (checked) => {
    getCheckboxes().forEach((checkbox) => {
        if (! checkbox.disabled) {
            checkbox.checked = checked;
        }
    });

    setBulkMode(checked ? 'all' : 'none');
    window.dispatchEvent(new CustomEvent('shield-set-state', { detail: checked }));
};

const compactPermissionStateForSubmit = () => {
    if (bulkMode === 'manual') {
        return;
    }

    getCheckboxModels().forEach((model) => {
        $wire.$set(model, [], false);
    });
};

setTimeout(() => {
    const toggle = $el.querySelector('.fi-fo-toggle[role=switch]');
    const form = $el.closest('form');

    if (toggle && toggle.getAttribute('aria-checked') === 'true') {
        setAllCheckboxes(true);
    }

    if (form) {
        form.addEventListener('submit', () => {
            compactPermissionStateForSubmit();
        });
    }
}, 200);

document.addEventListener('change', (event) => {
    const checkbox = event.target.closest(checkboxSelector);

    if (! checkbox) {
        return;
    }

    if (bulkMode === 'all') {
        syncManualStateFromDom();
    }

    setBulkMode('manual');
    updateToggleState();
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('.fi-fo-toggle[role=switch]');

    if (toggle) {
        setTimeout(() => {
            setAllCheckboxes(toggle.getAttribute('aria-checked') === 'true');
        }, 0);

        return;
    }

    if (event.target.closest('.fi-fo-checkbox-list-actions')) {
        setBulkMode('manual');
        updateToggleState();
    }
});
JS,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule): Unique => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isSystemRole())
                                    ->dehydrated(),

                                Select::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->options([
                                        'web'     => __('security::filament/resources/role.form.fields.web'),
                                        'sanctum' => __('security::filament/resources/role.form.fields.sanctum'),
                                    ])
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isSystemRole())
                                    ->dehydrated(),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default(Filament::getTenant()?->id)
                                    ->options(fn (): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn (): bool => ! (RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn (): bool => ! (RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled())),
                                Hidden::make('permissions_sync_mode')
                                    ->default('manual'),
                                static::getSelectAllFormComponent(),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                RoleResource::getShieldFormComponents(),
            ]);
    }

    private static function getSelectAllFormComponent(): Component
    {
        // The Toggle uses $wire.$entangle('data.select_all') internally.
        // We intentionally do NOT call tog.click() or use $watch('$wire.data.select_all')
        // anywhere — those were the cause of the stuck-loader loop after save.
        //
        // Instead, _chk() dispatches window event 'shield-set-state' which the Toggle
        // catches via x-on:shield-set-state.window and sets its own `state` directly.
        // Since the binding is deferred (not live), this queues the value for the next
        // form submit without firing an immediate Livewire network request.
        return Toggle::make('select_all')
            ->onIcon('heroicon-s-shield-check')
            ->offIcon('heroicon-s-shield-exclamation')
            ->label(__('filament-shield::filament-shield.field.select_all.name'))
            ->helperText(fn (): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
            ->dehydrated(fn (bool $state): bool => $state)
            ->extraAlpineAttributes(['x-on:shield-set-state.window' => 'state = $event.detail']);
    }
}
