<?php

return [
    'completed'      => 'Contabilidad configurada',
    'already-set-up' => 'La contabilidad ya está configurada',
    'no-template'    => 'No hay una empresa plantilla utilizable',
    'incomplete'     => 'La configuración contable no se completó',

    'descriptions' => [
        'completed'      => 'Se crearon el plan de cuentas, los diarios y los ajustes predeterminados para esta empresa.',
        'already-set-up' => 'Esta empresa ya tiene sus diarios de ventas y compras. No se modificó nada.',
        'no-template'    => 'Esta empresa es la propia empresa predeterminada utilizada como plantilla, o no existe ninguna empresa predeterminada, por lo que no hay de dónde copiar el plan de cuentas, los diarios y los ajustes predeterminados. No se creó nada. Defina como predeterminada otra empresa completamente configurada e inténtelo de nuevo.',
        'incomplete'     => 'A la empresa predeterminada utilizada como plantilla le falta un diario de ventas o de compras, o uno de sus ajustes predeterminados de cuenta, diario o impuesto está vacío o apunta a un registro que no le pertenece. No se creó nada para esta empresa. Complete la contabilidad de la empresa predeterminada e inténtelo de nuevo. Los detalles técnicos se han registrado.',
    ],
];
