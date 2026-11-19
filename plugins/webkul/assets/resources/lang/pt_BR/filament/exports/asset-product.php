<?php

return [
    'columns' => [
        'name'                    => 'Nome do Produto',
        'type'                    => 'Tipo de Produto',
        'category'                => 'Categoria',
        'model'                   => 'Modelo',
        'cost'                    => 'Custo',
        'assets-count'            => 'Ativos Registrados',
        'available-assets-count'  => 'Disponível no armazém',
        'held-assets-count'       => 'Distribuído em custódia',
        'terminated-assets-count' => 'Descartado / Perdido',
        'total-cost'              => 'Avaliação total',
        'is-active'               => 'Ativo',
        'company'                 => 'Empresa',
        'created-at'              => 'Criado em',
        'updated-at'              => 'Atualizado em',
    ],

    'notification' => [
        'completed' => 'A exportação de produtos foi concluída e :count linha(s) exportada(s).',
        'failed'    => ':count linha(s) falharam ao exportar.',
    ],
];
