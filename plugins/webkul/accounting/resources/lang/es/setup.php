<?php

return [
    'action'       => 'Configurar la contabilidad para esta empresa',
    'notice'       => 'La contabilidad aún no está configurada para esta empresa. Use \'Configurar la contabilidad\' abajo para crear su plan de cuentas, diarios y ajustes predeterminados.',
    'notification' => [
        'title' => 'Contabilidad configurada',
        'body'  => 'Se crearon el plan de cuentas, los diarios y los ajustes predeterminados para esta empresa.',
    ],
    'error' => [
        'title' => 'La configuración contable no se ejecutó',
        'body'  => 'No se crearon el plan de cuentas, los diarios ni los ajustes predeterminados. Compruebe que la empresa predeterminada utilizada como plantilla esté completamente configurada e inténtelo de nuevo. Los detalles técnicos se han registrado.',
    ],
];
