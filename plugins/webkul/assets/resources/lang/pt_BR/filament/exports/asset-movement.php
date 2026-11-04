<?php

return [
    'columns' => [
        'occurred-at'        => 'Data e hora',
        'asset-tag'          => 'Etiqueta do ativo',
        'product'            => 'Produto',
        'model'              => 'Modelo',
        'category'           => 'Categoria',
        'serial-no'          => 'Número de série',
        'type'               => 'Tipo de movimento',
        'quantity'           => 'Quantidade',
        'source-type'        => 'Tipo de origem',
        'from'               => 'De',
        'to'                 => 'Para',
        'condition'          => 'Condição',
        'expected-return-at' => 'Data prevista de devolução',
        'performed-by'       => 'Realizado por',
        'note'               => 'Nota',
        'company'            => 'Empresa',
    ],

    'options' => [
        'section'    => 'Filtrar Movimentações Exportadas (Opcional)',
        'date-from'  => 'Data Inicial',
        'date-until' => 'Data Final',
        'types'      => 'Tipos de Movimentação',
        'sites'      => 'Locais',
        'employees'  => 'Funcionários',
    ],

    'notification' => [
        'completed' => 'A exportação de movimentos de ativos foi concluída e :count linha(s) exportada(s).',
        'failed'    => ':count linha(s) falharam ao exportar.',
    ],
];
