<?php

return [
    'navigation' => [
        'title' => 'Mis presupuestos',
    ],

    'pages' => [
        'list' => [
            'title' => 'Mis presupuestos',
        ],
    ],

    'views' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'tabs' => [
        'opportunity' => 'Oportunidad',
    ],

    'board' => [
        'unauthorized' => 'No puedes mover este presupuesto a esa columna.',

        'card' => [
            'open'        => 'Abrir :name',
            'customer'    => 'Cliente',
            'total'       => 'Total',
            'date'        => 'Fecha del pedido',
            'salesperson' => 'Vendedor',
        ],
    ],

    'actions' => [
        'group' => [
            'label' => 'Acciones',
        ],

        'duplicate' => [
            'label'        => 'Duplicar',
            'notification' => ':count presupuesto(s) duplicado(s).',
        ],

        'mark-as-sent' => [
            'label'        => 'Marcar presupuesto como enviado',
            'notification' => ':count presupuesto(s) marcado(s) como enviado(s).',
        ],

        'send-email' => [
            'label'        => 'Enviar correo',
            'description'  => 'Cada presupuesto seleccionado se envía por correo a su cliente con el PDF adjunto. Los borradores se marcan como enviados.',
            'submit'       => 'Enviar',
            'subject'      => 'Presupuesto de :customer (Ref :name)',
            'body'         => 'Estimado/a :customer,<br><br>Su presupuesto <strong>:name</strong> por un importe de <strong>:amount</strong> está listo para revisar.<br><br>Si tiene alguna pregunta, no dude en contactarnos.',
            'notification' => ':sent presupuesto(s) enviado(s), :skipped omitido(s) (no es presupuesto, sin correo del cliente o sin acceso).',
        ],

        'confirm' => [
            'label'        => 'Confirmar',
            'description'  => 'Los presupuestos seleccionados se convierten en pedidos de venta.',
            'notification' => ':confirmed presupuesto(s) confirmado(s), :skipped omitido(s).',
        ],
    ],

    'team-filter' => [
        'label' => 'Equipo de ventas: :team',
        'clear' => 'Mostrar todo',
    ],
];
