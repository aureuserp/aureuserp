<?php

return [
    'columns' => [
        'name'         => 'Nombre',
        'code'         => 'Código',
        'custodian'    => 'Encargado del almacén',
        'assets-count' => 'Activos almacenados',
        'total-cost'   => 'Valoración total',
        'is-active'    => 'Activo',
        'company'      => 'Empresa',
        'creator'      => 'Creado por',
        'created-at'   => 'Fecha de creación',
        'updated-at'   => 'Fecha de actualización',
    ],

    'notification' => [
        'completed' => 'La exportación de almacenes se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
