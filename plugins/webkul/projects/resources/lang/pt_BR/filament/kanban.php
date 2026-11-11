<?php

return [
    'layouts' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'switcher' => [
        'label' => 'Disposição',
    ],

    'card' => [
        'title'        => 'Título',
        'priority'     => 'Prioridade',
        'quick-create' => 'Adicionar tarefa',
        'assignees'    => 'Responsáveis',
        'schedule'     => 'Agendar',
        'view'         => 'Ver',
        'edit'         => 'Editar',
        'delete'       => 'Excluir',
        'share'        => 'Copiar link',
        'shared'       => 'Link copiado',
        'tags-more'    => 'mais :count',
    ],

    'column' => [
        'add'               => 'Adicionar etapa',
        'add-project'       => 'Adicionar projeto',
        'edit-project'      => 'Editar projeto',
        'delete-project'    => 'Excluir projeto',
        'project-settings'  => 'Configurações do projeto',
        'private'           => 'Privada',
        'name'              => 'Nome',
        'edit'              => 'Editar etapa',
        'delete'            => 'Excluir etapa',
        'settings'          => 'Configurações da etapa',
        'fold'              => 'Recolher',
        'empty-heading'     => 'Sem tarefas',
        'empty-description' => 'Arraste uma tarefa aqui ou use + para criar uma.',
    ],

    'empty' => [
        'heading'     => 'Nenhuma etapa ainda',
        'description' => 'Crie uma etapa para começar a organizar as tarefas no quadro.',
    ],

    'progress' => [
        'label'   => 'Distribuição do estado das atividades',
        'overdue' => 'Atrasada',
        'today'   => 'Hoje',
        'planned' => 'Planejada',
        'none'    => 'Sem atividade',
    ],

    'schedule' => [
        'heading'  => 'Agendar tarefa',
        'deadline' => 'Prazo',
    ],

    'notifications' => [
        'moved'           => 'Tarefa movida',
        'state-updated'   => 'Estado atualizado',
        'task-created'    => 'Tarefa criada',
        'task-deleted'    => 'Tarefa excluída',
        'task-scheduled'  => 'Tarefa agendada',
        'project-created' => 'Projeto criado',
        'project-updated' => 'Projeto atualizado',
        'project-deleted' => 'Projeto excluído',
        'project-in-use'  => 'Este projeto ainda possui tarefas e não pode ser excluído',
        'stage-created'   => 'Etapa criada',
        'stage-updated'   => 'Etapa atualizada',
        'stage-in-use'    => 'Esta etapa ainda possui tarefas e não pode ser excluída',
        'stage-deleted'   => 'Etapa excluída',
    ],
];
