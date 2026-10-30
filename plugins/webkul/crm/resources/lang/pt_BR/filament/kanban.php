<?php

return [
    'add-column' => [
        'stage'         => 'Adicionar etapa',
        'activity-type' => 'Adicionar tipo de atividade',
        'add'           => 'Adicionar',
        'created'       => '":name" adicionado',
        'exists'        => '":name" já existe',
    ],

    'column-actions' => [
        'settings'           => 'Configurações: :name',
        'edit'               => 'Editar',
        'delete'             => 'Excluir',
        'name'               => 'Nome',
        'edit-heading'       => 'Editar ":name"',
        'delete-heading'     => 'Excluir ":name"?',
        'delete-description' => 'Esta ação não pode ser desfeita.',
        'saved'              => '":name" salvo',
        'deleted'            => '":name" excluído',

        'in-use' => [
            'title' => 'Não é possível excluir ":name"',
            'body'  => 'Ainda tem :count registro. Mova-o para outra coluna primeiro.|Ainda tem :count registros. Mova-os para outra coluna primeiro.',
        ],
    ],

    'empty' => [
        'heading'           => 'Nada aqui ainda',
        'description'       => 'Arraste um cartão para cá ou crie um novo.',
        'board-heading'     => 'Nenhum registro para mostrar',
        'board-description' => 'Nada corresponde a esta visualização ou aos filtros atuais.',
    ],

    'choose-group' => [
        'heading'     => 'Escolha um Agrupar por',
        'description' => 'A visualização Kanban mostra uma coluna por grupo. Escolha um campo em Agrupar por para ver o quadro.',
    ],

    'progress' => [
        'label' => 'Atividades nesta etapa',
        'total' => 'Receita esperada',
    ],
];
