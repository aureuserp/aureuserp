<?php

return [
    'layouts' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'switcher' => [
        'label' => 'Disposición',
    ],

    'card' => [
        'title'        => 'Título',
        'priority'     => 'Prioridad',
        'quick-create' => 'Añadir tarea',
        'assignees'    => 'Asignados',
        'schedule'     => 'Programar',
        'view'         => 'Ver',
        'edit'         => 'Editar',
        'delete'       => 'Eliminar',
        'share'        => 'Copiar enlace',
        'shared'       => 'Enlace copiado',
        'tags-more'    => ':count más',
    ],

    'column' => [
        'add'               => 'Añadir etapa',
        'add-project'       => 'Añadir proyecto',
        'edit-project'      => 'Editar proyecto',
        'delete-project'    => 'Eliminar proyecto',
        'project-settings'  => 'Ajustes del proyecto',
        'private'           => 'Privada',
        'name'              => 'Nombre',
        'edit'              => 'Editar etapa',
        'delete'            => 'Eliminar etapa',
        'settings'          => 'Ajustes de la etapa',
        'fold'              => 'Plegar',
        'empty-heading'     => 'Sin tareas',
        'empty-description' => 'Arrastra una tarea aquí o usa + para crear una.',
    ],

    'empty' => [
        'heading'     => 'Aún no hay etapas',
        'description' => 'Crea una etapa para empezar a organizar las tareas en el tablero.',
    ],

    'progress' => [
        'label'   => 'Distribución del estado de actividades',
        'overdue' => 'Vencido',
        'today'   => 'Hoy',
        'planned' => 'Planificado',
        'none'    => 'Sin actividad',
    ],

    'schedule' => [
        'heading'  => 'Programar tarea',
        'deadline' => 'Fecha límite',
    ],

    'notifications' => [
        'moved'           => 'Tarea movida',
        'state-updated'   => 'Estado actualizado',
        'task-created'    => 'Tarea creada',
        'task-deleted'    => 'Tarea eliminada',
        'task-scheduled'  => 'Tarea programada',
        'project-created' => 'Proyecto creado',
        'project-updated' => 'Proyecto actualizado',
        'project-deleted' => 'Proyecto eliminado',
        'project-in-use'  => 'Este proyecto todavía tiene tareas y no se puede eliminar',
        'stage-created'   => 'Etapa creada',
        'stage-updated'   => 'Etapa actualizada',
        'stage-in-use'    => 'Esta etapa todavía tiene tareas y no se puede eliminar',
        'stage-deleted'   => 'Etapa eliminada',
    ],
];
