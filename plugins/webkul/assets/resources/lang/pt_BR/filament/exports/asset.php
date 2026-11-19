<?php

return [
    'columns' => [
        'asset-tag'          => 'Etiqueta do ativo',
        'product'            => 'Produto',
        'product-type'       => 'Tipo de Produto',
        'initial-quantity'   => 'Quantidade inicial',
        'quantity'           => 'Quantidade',
        'model'              => 'Modelo',
        'category'           => 'Categoria',
        'serial-no'          => 'Número de série',
        'status'             => 'Status',
        'condition'          => 'Condição',
        'warehouse'          => 'Armazém',
        'current-employee'   => 'Funcionário atual',
        'department'         => 'Departamento',
        'current-site'       => 'Local atual',
        'site-type'          => 'Tipo de local',
        'issued-at'          => 'Emitido em',
        'expected-return-at' => 'Data prevista de devolução',
        'cost'               => 'Custo',
        'purchase-date'      => 'Data de compra',
        'warranty-until'     => 'Garantia até',
        'custody-note'       => 'Nota de custódia',
        'note'               => 'Nota',
        'company'            => 'Empresa',
        'created-at'         => 'Criado em',
    ],

    'options' => [
        'section'    => 'Filtrar Ativos Exportados (Opcional)',
        'products'   => 'Produtos',
        'statuses'   => 'Status',
        'conditions' => 'Condições',
        'categories' => 'Categorias',
        'warehouses' => 'Armazéns',
        'sites'      => 'Locais',
        'employees'  => 'Funcionários',
    ],

    'notification' => [
        'completed' => 'A exportação do seu ativo foi concluída e :count linha(s) exportada(s).',
        'failed'    => ':count linha(s) falharam ao exportar.',
    ],
];
