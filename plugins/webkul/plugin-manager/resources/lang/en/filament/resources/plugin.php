<?php

return [

    'navigation' => [
        'group' => 'Plugins',
    ],

    'title' => 'Plugin',

    'table' => [
        'version'             => 'Version',
        'dependencies'        => 'Dependencies',
        'dependencies_suffix' => 'Dependencies',
    ],

    'status' => [
        'installed'     => 'Installed',
        'not_installed' => 'Not Installed',
    ],

    'filters' => [
        'installation_status' => 'Installation Status',
        'all_plugins'         => 'All Plugins',
        'installed'           => 'Installed',
        'not_installed'       => 'Not Installed',
        'active_status'       => 'Active Status',
        'author'              => 'Author',
        'webkul'              => 'Webkul',
        'third_party'         => 'Third Party',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Install',
            'heading'     => 'Install Plugin :name',
            'description' => "Are you sure you want to install the ':name' plugin? This will run migrations and seeders.",
            'submit'      => 'Install Plugin',
        ],
        'uninstall' => [
            'title'      => 'Uninstall',
            'heading'    => 'Uninstall Plugin',
            'submit'     => 'Uninstall Plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin Installed Successfully',
            'body'  => "The ':name' plugin has been installed.",
        ],
        'installed-failed' => [
            'title' => 'Installation Failed',
        ],
        'uninstalled' => [
            'title' => 'Plugin Uninstalled Successfully',
            'body'  => "The ':name' plugin has been uninstalled.",
        ],
        'uninstalled-failed' => [
            'title' => 'Uninstallation Failed',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Plugin Information',
            'dependencies' => 'Dependencies',
        ],
        'name'         => 'Plugin Name',
        'version'      => 'Version',
        'dependencies' => 'Required Plugins',
        'dependents'   => 'Plugins That Depend On This',
        'is_installed' => 'Installation Status',
        'license'      => 'License',
        'summary'      => 'Description',

        'dependencies-repeater' => [
            'title'        => 'Required Plugins',
            'name'         => 'Plugin Name',
            'is_installed' => 'Installed',
            'placeholder'  => 'No dependencies required',
        ],

        'dependents-repeater' => [
            'title'        => 'Plugins That Depend On This',
            'name'         => 'Plugin Name',
            'is_installed' => 'Installed',
            'placeholder'  => 'No dependents',
        ],

    ],

];
