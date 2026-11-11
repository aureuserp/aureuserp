<?php

return [
    'layouts' => [
        'list'   => 'List',
        'kanban' => 'Kanban',
        'graph'  => 'Graph',
    ],

    'switcher' => [
        'label' => 'View layout',
    ],

    'card' => [
        'title'        => 'Title',
        'priority'     => 'Priority',
        'quick-create' => 'Add task',
        'assignees'    => 'Assignees',
        'schedule'     => 'Schedule',
        'view'         => 'View',
        'edit'         => 'Edit',
        'delete'       => 'Delete',
        'share'        => 'Copy link',
        'shared'       => 'Link copied',
        'tags-more'    => ':count more',
    ],

    'column' => [
        'add'               => 'Add stage',
        'add-project'       => 'Add project',
        'edit-project'      => 'Edit project',
        'delete-project'    => 'Delete project',
        'project-settings'  => 'Project settings',
        'private'           => 'Private',
        'name'              => 'Name',
        'edit'              => 'Edit stage',
        'delete'            => 'Delete stage',
        'settings'          => 'Stage settings',
        'fold'              => 'Fold',
        'empty-heading'     => 'No tasks',
        'empty-description' => 'Drag a task here or use + to create one.',
    ],

    'empty' => [
        'heading'     => 'No stages yet',
        'description' => 'Create a stage to start organising tasks on the board.',
    ],

    'progress' => [
        'label'   => 'Activity status distribution',
        'overdue' => 'Overdue',
        'today'   => 'Today',
        'planned' => 'Planned',
        'none'    => 'No activity',
    ],

    'schedule' => [
        'heading'  => 'Schedule task',
        'deadline' => 'Deadline',
    ],

    'notifications' => [
        'moved'           => 'Task moved',
        'state-updated'   => 'State updated',
        'task-created'    => 'Task created',
        'task-deleted'    => 'Task deleted',
        'task-scheduled'  => 'Task scheduled',
        'project-created' => 'Project created',
        'project-updated' => 'Project updated',
        'project-deleted' => 'Project deleted',
        'project-in-use'  => 'This project still has tasks and cannot be deleted',
        'stage-created'   => 'Stage created',
        'stage-updated'   => 'Stage updated',
        'stage-in-use'    => 'This stage still has tasks and cannot be deleted',
        'stage-deleted'   => 'Stage deleted',
    ],
];
