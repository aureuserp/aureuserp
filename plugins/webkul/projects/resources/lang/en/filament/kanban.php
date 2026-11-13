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
        'none'              => 'None',
        'name'              => 'Name',
        'edit'              => 'Edit stage',
        'delete'            => 'Delete stage',
        'settings'          => 'Stage settings',
        'empty-heading'     => 'No tasks',
        'empty-description' => 'Drag a task here or use + to create one.',
    ],

    'group' => [
        'project'   => 'Project',
        'stage'     => 'Stage',
        'partner'   => 'Customer',
        'company'   => 'Company',
        'milestone' => 'Milestone',
        'state'     => 'Status',
        'priority'  => 'Priority',
    ],

    'priority' => [
        'high'   => 'Starred',
        'normal' => 'Normal',
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

    'activities' => [
        'heading'     => 'Scheduled activities',
        'schedule'    => 'Schedule activity',
        'deadline'    => 'Due date',
        'assigned-to' => 'Assigned to',
    ],

    'assign' => [
        'heading'      => 'Assign task',
        'label'        => 'Assignees',
        'save'         => 'Assign',
        'view-profile' => 'View profile',
    ],

    'graph' => [
        'types' => [
            'bar'  => 'Bar chart',
            'line' => 'Line chart',
            'pie'  => 'Pie chart',
        ],
        'stacked'   => 'Stack by state',
        'sort-asc'  => 'Sort ascending',
        'sort-desc' => 'Sort descending',
    ],

    'notifications' => [
        'moved'             => 'Task moved',
        'state-updated'     => 'State updated',
        'assignees-updated' => 'Assignees updated',
        'task-created'      => 'Task created',
        'task-deleted'      => 'Task deleted',
        'task-scheduled'    => 'Task scheduled',
        'project-created'   => 'Project created',
        'project-updated'   => 'Project updated',
        'project-deleted'   => 'Project deleted',
        'project-in-use'    => 'This project still has tasks and cannot be deleted',
        'stage-created'     => 'Stage created',
        'stage-updated'     => 'Stage updated',
        'stage-in-use'      => 'This stage still has tasks and cannot be deleted',
        'stage-deleted'     => 'Stage deleted',
    ],
];
