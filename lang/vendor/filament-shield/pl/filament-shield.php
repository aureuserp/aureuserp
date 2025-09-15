<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nazwa',
    'column.guard_name' => 'Nazwa Guarda',
    'column.team' => 'Zespół',
    'column.roles' => 'Role',
    'column.permissions' => 'Uprawnienia',
    'column.updated_at' => 'Data aktualizacji',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nazwa',
    'field.guard_name' => 'Nazwa Guarda',
    'field.permissions' => 'Uprawnienia',
    'field.team' => 'Zespół',
    'field.team.placeholder' => 'Wybierz zespół...',
    'field.select_all.name' => 'Zaznacz wszystko',
    'field.select_all.message' => 'Włącza/wyłącza wszystkie uprawnienia dla tej roli',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Role',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rola',
    'resource.label.roles' => 'Role',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Komponenty',
    'resources' => 'Zasoby',
    'widgets' => 'Widgety',
    'pages' => 'Strony',
    'custom' => 'Własne uprawnienia',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nie masz uprawnień do dostępu',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Podgląd',
        'view_any' => 'Podgląd dowolnego',
        'create' => 'Tworzenie',
        'update' => 'Edycja',
        'delete' => 'Usuwanie',
        'delete_any' => 'Usuwanie dowolnego',
        'force_delete' => 'Wymuszone usunięcie',
        'force_delete_any' => 'Wymuszone usunięcie dowolnego',
        'restore' => 'Przywracanie',
        'restore_any' => 'Przywracanie dowolnego',
        'reorder' => 'Zmienianie kolejności',
        'replicate' => 'Duplikowanie',
    ],
];
