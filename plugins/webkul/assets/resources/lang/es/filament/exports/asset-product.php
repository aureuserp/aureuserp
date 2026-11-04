<?php

return [
    'columns' => [
        'name'                    => 'Nombre del Producto',
        'type'                    => 'Tipo de Producto',
        'category'                => 'Categoría',
        'model'                   => 'Modelo',
        'cost'                    => 'Costo',
        'assets-count'            => 'Activos Registrados',
        'available-assets-count'  => 'Disponible en almacén',
        'held-assets-count'       => 'Asignado en custodia',
        'terminated-assets-count' => 'Desguazado / Perdido',
        'total-cost'              => 'Valoración total',
        'is-active'               => 'Activo',
        'company'                 => 'Empresa',
        'created-at'              => 'Creado el',
        'updated-at'              => 'Actualizado el',
    ],

    'notification' => [
        'completed' => 'La exportación de productos se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
