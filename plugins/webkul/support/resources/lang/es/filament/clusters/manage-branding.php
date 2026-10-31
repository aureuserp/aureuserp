<?php

return [
    'breadcrumb' => 'Personalización de marca',
    'title'      => 'Personalización de marca',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Personalización de marca',
    ],

    'form' => [
        'tabs' => [
            'colors' => 'Colores de marca',
            'logo'   => 'Logotipo y favicon',
        ],

        'sections' => [
            'colors' => [
                'title'       => 'Colores de marca',
                'description' => 'Define los colores utilizados en los paneles de administración y de cliente. Haz clic en el icono de información de cualquier color para previsualizar cómo se ve antes de guardar.',
            ],
            'palettes' => [
                'title'       => 'Paletas de colores sugeridas',
                'description' => 'Elige entre nuestras paletas de colores prediseñadas o crea la tuya propia.',
            ],
            'logo' => [
                'title'       => 'Logotipo y favicon',
                'description' => 'Sobrescribir los logotipos y el favicon utilizados en los paneles de administración y de cliente. Dejar un campo vacío para conservar el valor predeterminado.',
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

        'palettes' => [
            'coral'       => 'Rojo coral',
            'ocean'       => 'Azul océano',
            'forest'      => 'Verde bosque',
            'purple'      => 'Sueño púrpura',
            'modern'      => 'Gris moderno',
            'apply'       => 'Usar este',
            'custom-hint' => '¿Necesitas algo diferente? Define tus propios colores con los selectores de arriba.',
        ],

        'preview' => [
            'title'             => 'Vista previa en vivo',
            'heading'           => 'Vista previa de colores',
            'description'       => 'Previsualiza cómo se verán tus colores en toda la interfaz antes de guardar.',
            'hint'              => 'Haz clic en el icono de información de cualquier color para ver una vista previa en vivo de cómo se ven los colores seleccionados en toda la aplicación.',
            'shade-note'        => 'Tono 600 (usado para botones)',
            'light'             => 'Tema claro',
            'dark'              => 'Tema oscuro',
            'search'            => 'Buscar...',
            'primary-button'    => 'Botón primario',
            'secondary-button'  => 'Botón secundario',
            'alert'             => 'Este es un mensaje informativo.',
            'card-title'        => 'Título de la tarjeta',
            'card-body'         => 'Este es un contenido de ejemplo para mostrar cómo se ven el texto y las superficies con los colores seleccionados.',
            'link'              => 'Ver detalles',
            'input-label'       => 'Texto del encabezado',
            'input-placeholder' => 'Campo de entrada',

            'nav' => [
                'dashboard' => 'Panel',
                'products'  => 'Productos',
                'orders'    => 'Pedidos',
                'customers' => 'Clientes',
                'settings'  => 'Configuración',
            ],

            'status' => [
                'success' => 'Éxito',
                'info'    => 'Información',
                'warning' => 'Advertencia',
                'danger'  => 'Peligro',
            ],
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Restablecer al valor predeterminado',
        ],
    ],
];
