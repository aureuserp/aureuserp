<?php

return [
    'model-label'        => 'Cliente',
    'plural-model-label' => 'Clientes',

    'navigation' => [
        'title' => 'Clientes',
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nombre',
            'phone'       => 'Teléfono',
            'email'       => 'Correo',
            'salesperson' => 'Comercial',
            'city'        => 'Ciudad',
            'country'     => 'País',
            'company'     => 'Empresa',
        ],
    ],

    'grid' => [
        'opportunities' => '{1} :count oportunidad|[0,*] :count oportunidades',
        'sale-orders'   => '{1} :count pedido de venta|[0,*] :count pedidos de venta',
    ],

    'board' => [
        'unauthorized' => 'Este cliente no se puede mover a esa columna.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Fusionar',
            'heading'            => 'Fusionar clientes',
            'description'        => 'Todo lo vinculado a los demás clientes (oportunidades, pedidos, contactos hijos, mensajes…) pasa al cliente que conservas. Sus campos vacíos se rellenan con los de los demás, y los demás se archivan.',
            'submit'             => 'Fusionar clientes',
            'destination'        => 'Cliente a conservar',
            'destination-helper' => 'Los demás clientes seleccionados se fusionan en este.',
            'invalid'            => 'Selecciona al menos dos clientes que puedas editar y archivar.',
            'notification'       => ':count cliente(s) fusionado(s) en :name.',
            'log'                => 'Clientes fusionados: :names',
        ],

        'vcard' => [
            'label' => 'Descargar (vCard)',
        ],

        'portal' => [
            'label'        => 'Conceder acceso al portal',
            'description'  => 'Cada cliente seleccionado con correo recibe una invitación para definir una contraseña del portal.',
            'notification' => 'Acceso al portal concedido a :granted cliente(s), :skipped omitido(s) (sin correo, ya tiene acceso, correo en uso o sin permiso).',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Resumen',
        ],

        'fields' => [
            'opportunities' => 'Oportunidades',
            'sale-orders'   => 'Pedidos de venta',
        ],
    ],
];
