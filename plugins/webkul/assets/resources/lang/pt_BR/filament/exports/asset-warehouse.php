<?php

return [
    'columns' => [
        'name'         => 'Nome',
        'code'         => 'Código',
        'custodian'    => 'Fiel depositário',
        'assets-count' => 'Ativos armazenados',
        'total-cost'   => 'Avaliação total',
        'is-active'    => 'Ativo',
        'company'      => 'Empresa',
        'creator'      => 'Criado por',
        'created-at'   => 'Data de criação',
        'updated-at'   => 'Data de atualização',
    ],

    'notification' => [
        'completed' => 'A exportação de armazéns foi concluída e :count linha(s) exportada(s).',
        'failed'    => ':count linha(s) falharam ao exportar.',
    ],
];
