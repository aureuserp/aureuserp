<?php

return [
    'model-label'        => 'Regla de visitantes a leads',
    'plural-model-label' => 'Reglas de visitantes a leads',

    'navigation' => [
        'title' => 'Reglas de visitantes a leads',
        'group' => 'Generación de leads',
    ],

    'form' => [
        'name'                     => 'Nombre de la regla',
        'name-placeholder'         => 'p. ej. Visitantes de EE. UU.',
        'active'                   => 'Activa',
        'countries'                => 'Países',
        'countries-helper'         => 'Obtenido de la dirección IP del visitante. Vacío = todos los países.',
        'states'                   => 'Estados',
        'states-helper'            => 'Solo se conocen para clientes conectados (de su dirección). Vacío = todos los estados.',
        'url'                      => 'Expresión de URL',
        'url-placeholder'          => 'p. ej. /pagina',
        'url-helper'               => 'Páginas que cuentan, como expresión regular sobre la ruta. Vacío = todo el sitio, "/" = solo la página de inicio, "/pricing|/shop" = páginas que contienen /pricing o /shop.',
        'url-invalid'              => 'Introduzca una expresión válida.',
        'type'                     => 'Tipo',
        'suffix'                   => 'Sufijo',
        'suffix-helper'            => 'Se añade al nombre del lead, p. ej. "Visitante del sitio #12 (India) - Precios".',
        'team'                     => 'Equipo de ventas',
        'salesperson'              => 'Vendedor',
        'tags'                     => 'Etiquetas',
        'priority'                 => 'Prioridad',
        'company'                  => 'Empresa',
        'company-helper'           => 'Vacío = la empresa del cliente o la primera empresa activa.',
        'lead-for'                 => 'Seguimiento de datos',
        'industries'               => 'Sectores',
        'industries-helper'        => 'Escriba un nombre para añadir un sector.',
        'filter-on-size'           => 'Filtrar por tamaño',
        'size-min'                 => 'Tamaño de empresa desde',
        'size-max'                 => 'Tamaño de empresa hasta',
        'employees'                => 'empleados',
        'gdpr'                     => 'Asegúrese de saber si debe cumplir el RGPD para almacenar datos personales.',
        'contact-filter'           => 'Filtrar contactos por',
        'extra-contacts'           => 'Número de contactos',
        'extra-contacts-helper'    => 'De 1 a 5 contactos por empresa.',
        'preferred-role'           => 'Rol preferido',
        'other-roles'              => 'Otros roles',
        'seniority'                => 'Antigüedad',

        'sections' => [
            'traffic'          => 'Condiciones de tráfico del sitio',
            'generation'       => 'Condiciones de generación de oportunidades',
            'lead-generation'  => 'Condiciones de generación de leads',
            'contact-filter'   => 'Filtro de contactos',
            'lead-data'        => 'Datos del lead',
            'opportunity-data' => 'Datos de la oportunidad',
            'settings'         => 'Configuración',
        ],
    ],

    'table' => [
        'name'          => 'Nombre de la regla',
        'url'           => 'Expresión de URL',
        'whole-website' => 'Todo el sitio',
        'countries'     => 'Países',
        'all-countries' => 'Todos los países',
        'type'          => 'Tipo',
        'team'          => 'Equipo de ventas',
        'salesperson'   => 'Vendedor',
        'leads'         => 'Leads',
        'opportunities' => 'Oportunidades',
        'active'        => 'Activa',
    ],

    'pages' => [
        'leads' => [
            'navigation' => 'Leads generados',
            'title'      => 'Leads generados: :rule',

            'columns' => [
                'name'       => 'Nombre',
                'country'    => 'País',
                'stage'      => 'Etapa',
                'created-at' => 'Creado',
            ],
        ],
    ],
];
