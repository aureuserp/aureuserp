<?php

return [
    'actions' => [
        'export' => [
            'label' => 'Exportar',
        ],

        'import' => [
            'label' => 'Importar',
        ],
    ],

    'columns' => [
        'id'                 => 'ID',
        'name'               => 'Nombre',
        'contact-name'       => 'Nombre de contacto',
        'company-name'       => 'Nombre de la empresa',
        'job-title'          => 'Cargo',
        'email'              => 'Correo electrónico',
        'phone'              => 'Teléfono',
        'website'            => 'Sitio web',
        'street1'            => 'Calle',
        'street2'            => 'Calle 2',
        'city'               => 'Ciudad',
        'zip'                => 'Código postal',
        'country'            => 'País',
        'stage'              => 'Etapa',
        'salesperson'        => 'Comercial',
        'team'               => 'Equipo de ventas',
        'priority'           => 'Prioridad',
        'probability'        => 'Probabilidad',
        'tags'               => 'Etiquetas',
        'source'             => 'Origen',
        'medium'             => 'Medio',
        'campaign'           => 'Campaña',
        'notes'              => 'Notas',
        'lost-reason'        => 'Motivo de pérdida',
        'created-at'         => 'Creado el',
        'customer'           => 'Cliente',
        'expected-revenue'   => 'Ingreso esperado',
        'prorated-revenue'   => 'Ingreso prorrateado',
        'expected-closing'   => 'Cierre previsto',
        'number'             => 'Número',
        'order-date'         => 'Fecha del pedido',
        'expiration'         => 'Vencimiento',
        'delivery-date'      => 'Fecha de entrega',
        'customer-reference' => 'Referencia del cliente',
        'source-document'    => 'Documento origen',
        'untaxed-amount'     => 'Base imponible',
        'taxes'              => 'Impuestos',
        'total'              => 'Total',
        'currency'           => 'Moneda',
        'status'             => 'Estado',
        'invoice-status'     => 'Estado de facturación',
    ],

    'notifications' => [
        'export' => [
            'completed' => 'Tu exportación está lista: :count filas exportadas.',
            'failed'    => ':count filas no se pudieron exportar.',
        ],

        'import' => [
            'completed' => 'Tu importación ha terminado: :count filas importadas.',
            'failed'    => ':count filas fallaron; descárgalas desde esta notificación para ver el motivo.',
        ],
    ],

    'errors' => [
        'record-not-found' => 'No se encontró ningún registro con ID :id en tus empresas.',
        'cannot-update'    => 'No tienes permiso para actualizar el registro :id.',
        'cannot-create'    => 'No tienes permiso para crear registros.',
        'not-found'        => 'No se encontró :field ":value".',
    ],
];
