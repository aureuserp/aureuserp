<?php

return [
    'model-label'        => 'Equipo de ventas',
    'plural-model-label' => 'Equipos de ventas',

    'navigation' => [
        'title' => 'Equipos',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Equipo de ventas',
            'open-opportunities' => 'Oportunidades abiertas',
            'expected-revenue'   => 'Ingresos esperados',
            'members'            => 'Miembros',
            'company'            => 'Empresa',
        ],

        'filters' => [
            'team-leader' => 'Líder del equipo',
            'created-at'  => 'Creado el',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Pipeline',
            ],
        ],
    ],

    'grid' => [
        'new-opportunities'  => '{1} :count nueva oportunidad|[0,*] :count nuevas oportunidades',
        'quotations'         => '{1} :count presupuesto|[0,*] :count presupuestos',
        'configuration'      => 'Configuración',
        'menu'               => 'Menú del equipo',
        'open-opportunities' => '{1} :count oportunidad abierta|[0,*] :count oportunidades abiertas',
        'orders-to-invoice'  => '{1} :count pedido por facturar|[0,*] :count pedidos por facturar',
        'chart'              => 'Nuevas oportunidades por semana',
        'target-progress'    => 'Facturado este mes: :invoiced de :target',
    ],

    'grid-menu' => [
        'view'          => 'Ver',
        'reporting'     => 'Informes',
        'leads'         => 'Clientes potenciales',
        'opportunities' => 'Oportunidades',
        'quotations'    => 'Presupuestos',
        'sales-orders'  => 'Pedidos de venta',
        'forecast'      => 'Previsión',
        'activities'    => 'Actividades',
        'no-color'      => 'Sin color',
    ],
];
