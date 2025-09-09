<?php

return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug'                       => 'shield/roles',
        'navigation_sort'            => -1,
        'navigation_badge'           => false,
        'navigation_group'           => true,
        'is_globally_searchable'     => false,
        'show_model_path'            => false,
        'is_scoped_to_tenant'        => true,
        'cluster'                    => null,
    ],

    'auth_provider_model' => [
        'fqcn' => 'Webkul\\Security\\Models\\User',
    ],

    'super_admin' => [
        'enabled'         => false,
        'name'            => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate'  => 'before',
    ],

    'panel_user' => [
        'enabled' => true,
        'name'    => 'Admin',
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ],

        'page'   => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages'              => true,
        'widgets'            => true,
        'resources'          => true,
        'custom_permissions' => false,
    ],

    'generator' => [
        'option'           => 'policies_and_permissions',
        'policy_directory' => 'Policies',
        'policy_namespace' => 'Policies',
    ],

    'exclude' => [
        'enabled' => true,

        'pages' => [
            'Dashboard',
        ],

        'widgets' => [
            'AccountWidget',
            'FilamentInfoWidget',
        ],

        'resources' => [],
    ],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets'   => false,
        'discover_all_pages'     => false,
    ],

    'register_role_policy' => [
        'enabled' => true,
    ],
];
