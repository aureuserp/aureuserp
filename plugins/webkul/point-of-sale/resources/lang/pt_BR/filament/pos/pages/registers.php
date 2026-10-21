<?php

return [
    'title'      => 'Caixas',

    'navigation' => [
        'label' => 'Caixas',
    ],

    'session' => [
        'open'   => 'Aberta por :name',
        'closed' => 'Fechada',
    ],

    'actions' => [
        'open'    => 'Abrir caixa',
        'resume'  => 'Retomar',

        'discard' => [
            'label'        => 'Descartar sessão',
            'heading'      => 'Descartar esta sessão?',
            'description'  => 'O caixa foi aberto, mas nada foi vendido nele. Descartar remove a sessão para que ela possa ser aberta novamente do zero.',
            'confirm'      => 'Descartar',

            'notification' => [
                'title' => 'Sessão descartada',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'Nenhum ponto de venda disponível',
        'description' => 'Crie um ponto de venda ativo no back office para começar a vender.',
    ],
];
