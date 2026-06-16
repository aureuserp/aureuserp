<?php

return [

    'uninstall' => [
        'title'   => 'Uninstall Confirmation',
        'message' => 'Are you sure you want to uninstall the :name plugin?',
        'warning' => '⚠️ This action cannot be undone and will permanently delete data.',
    ],

    'dependents' => [
        'title'         => 'Dependent Plugins',
        'description'   => 'These plugins depend on this one and will also be uninstalled.',
        'installed'     => 'Installed',
        'not_installed' => 'Not Installed',
    ],

    'dependency_warning' => [
        'title'   => 'Action Required',
        'message' => '⚠️ Please uninstall the following dependent plugins first before uninstalling :name.',
    ],

    'data_impact' => [
        'title'       => 'Data Impact',
        'description' => 'The following database tables contain data that will be permanently deleted.',
        'records'     => ':count records',
    ],

];
