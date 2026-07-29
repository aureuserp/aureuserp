<?php

return [

    'uninstall' => [
        'title'   => 'Confirmación de desinstalación',
        'message' => '¿Está seguro de que desea desinstalar el plugin :name?',
        'warning' => '⚠️ Esta acción no se puede deshacer y eliminará los datos de forma permanente.',
    ],

    'dependents' => [
        'title'         => 'Plugins dependientes',
        'description'   => 'Estos plugins dependen de este. Los dependientes instalados deben desinstalarse primero.',
        'installed'     => 'Instalado',
        'not_installed' => 'No instalado',
    ],

    'dependency_warning' => [
        'title'   => 'Acción requerida',
        'message' => '⚠️ Desinstale primero los siguientes plugins dependientes antes de desinstalar :name.',
    ],

    'data_impact' => [
        'title'       => 'Impacto en los datos',
        'description' => 'Las siguientes tablas de la base de datos contienen datos que se eliminarán de forma permanente.',
        'records'     => ':count registros',
    ],

];
