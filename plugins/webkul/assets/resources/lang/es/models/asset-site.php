<?php

return [
    'title' => 'Sitio de activos y custodias',

    'deletion' => [
        'referenced' => 'No se puede eliminar un sitio que esté referenciado por registros de custodia o activos.',
    ],

    'deactivation' => [
        'open-custody' => 'No se puede desactivar un sitio mientras mantenga registros de custodia abiertos.',
    ],

    'company' => [
        'immutable' => 'No se puede cambiar la empresa de un sitio que esté referenciado por registros de custodia o activos.',
    ],

    'hierarchy' => [
        'circular-reference' => 'Un sitio no puede ser establecido como hijo de sí mismo o de uno de sus descendientes.',
    ],

    'usage' => [
        'has-fixed-assets'   => 'No se puede cambiar el tipo de uso del sitio a zona de custodia mientras este o sus subsitios tengan activos fijos desplegados sin un empleado.',
        'has-custody-assets' => 'No se puede cambiar el tipo de uso del sitio a ubicación de activos mientras este o sus subsitios tengan activos de custodia de empleados activos.',
    ],
];
