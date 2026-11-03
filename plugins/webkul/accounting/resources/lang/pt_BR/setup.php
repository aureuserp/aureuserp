<?php

return [
    'action'       => 'Configurar a contabilidade para esta empresa',
    'notice'       => 'A contabilidade ainda não está configurada para esta empresa. Use \'Configurar contabilidade\' abaixo para criar seu plano de contas, diários e configurações padrão.',
    'results'      => [
        'failed' => [
            'title' => 'A configuração contábil falhou',
            'body'  => 'Um erro inesperado interrompeu a configuração e nada foi criado para esta empresa. Tente novamente ou contate seu administrador. Os detalhes técnicos foram registrados.',
        ],

        'no-company' => [
            'title' => 'Nenhuma empresa selecionada',
            'body'  => 'Selecione a empresa para a qual deseja configurar a contabilidade e tente novamente.',
        ],
    ],
];
