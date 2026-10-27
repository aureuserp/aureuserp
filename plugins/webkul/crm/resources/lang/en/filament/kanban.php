<?php

return [
    'add-column' => [
        'stage'         => 'Add Stage',
        'activity-type' => 'Add Activity Type',
        'add'           => 'Add',
        'created'       => '":name" added',
        'exists'        => '":name" already exists',
    ],

    'column-actions' => [
        'settings'           => 'Settings: :name',
        'edit'               => 'Edit',
        'delete'             => 'Delete',
        'name'               => 'Name',
        'edit-heading'       => 'Edit ":name"',
        'delete-heading'     => 'Delete ":name"?',
        'delete-description' => 'This can\'t be undone.',
        'saved'              => '":name" saved',
        'deleted'            => '":name" deleted',

        'in-use' => [
            'title' => '":name" can\'t be deleted',
            'body'  => 'It still has :count record. Move it to another column first.|It still has :count records. Move them to another column first.',
        ],
    ],

    'empty' => [
        'heading'           => 'Nothing here yet',
        'description'       => 'Drag a card here or create a new one.',
        'board-heading'     => 'No records to show',
        'board-description' => 'Nothing matches this view or the current filters.',
    ],

    'progress' => [
        'label' => 'Activities in this stage',
        'total' => 'Expected revenue',
    ],
];
