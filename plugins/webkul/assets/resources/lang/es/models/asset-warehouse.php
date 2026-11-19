<?php

return [
    'title'        => 'Almacén de activos',
    'deletion'     => [
        'referenced' => 'No se puede eliminar un almacén al que hacen referencia registros de custodia o activos.',
    ],
    'deactivation' => [
        'has-assets' => 'No se puede desactivar un almacén mientras contenga activos almacenados.',
    ],
    'company'      => [
        'immutable' => 'No se puede cambiar la empresa de un almacén referenciado por registros de custodia o activos.',
    ],
];
