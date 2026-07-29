<?php

return [
    'breadcrumb' => 'Personalización de marca',
    'title'      => 'Personalización de marca',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Personalización de marca',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Logotipo y favicon',
                'description' => 'Sobrescribir los logotipos, el favicon y la altura del logotipo utilizados en los paneles de administración y de cliente. Dejar un campo vacío para conservar el valor predeterminado.',
            ],
            'colors' => [
                'title'       => 'Colores',
                'description' => 'Sobrescribir los colores del tema utilizados en los paneles de administración y de cliente. Dejar un color vacío para conservar el valor predeterminado.',
            ],
        ],
        'fields' => [
            'light-logo'         => 'Logotipo claro',
            'light-logo-helper'  => 'Se muestra sobre fondos claros. Reemplaza el logotipo predeterminado.',
            'dark-logo'          => 'Logotipo oscuro',
            'dark-logo-helper'   => 'Se muestra cuando el modo oscuro está activado.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Icono de la pestaña del navegador.',
            'logo-height'        => 'Altura del logotipo',
            'logo-height-helper' => 'Un valor de altura CSS, p. ej. 2rem o 40px.',
            'primary-color'      => 'Primario',
            'gray-color'         => 'Gris',
            'danger-color'       => 'Peligro',
            'info-color'         => 'Información',
            'success-color'      => 'Éxito',
            'warning-color'      => 'Advertencia',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Restablecer al valor predeterminado',
        ],
    ],
];
