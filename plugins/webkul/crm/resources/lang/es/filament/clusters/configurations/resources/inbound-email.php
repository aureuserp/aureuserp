<?php

return [
    'model-label'        => 'Correo recibido',
    'plural-model-label' => 'Correos recibidos',

    'navigation' => [
        'title' => 'Correos recibidos',
    ],

    'table' => [
        'columns' => [
            'received-at' => 'Recibido',
            'from'        => 'De',
            'subject'     => 'Asunto',
            'status'      => 'Resultado',
            'team'        => 'Equipo de ventas',
            'lead'        => 'Lead / Oportunidad',
            'reason'      => 'Motivo',
            'recipients'  => 'Destinatarios',
            'server'      => 'Servidor de correo',
        ],

        'empty' => [
            'heading'     => 'Aún no se ha recibido ningún correo',
            'description' => 'Aquí se muestran los correos leídos de los servidores de correo entrante y qué se hizo con cada uno.',
        ],
    ],
];
