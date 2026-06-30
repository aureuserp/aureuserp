<?php

return [

    'navigation' => [
        'group' => 'Complementos',
    ],

    'title' => 'Plugin',

    'table' => [
        'version'             => 'Versión',
        'dependencies'        => 'Dependencias',
        'dependencies_suffix' => ' Dependencias',
    ],

    'status' => [
        'installed'     => 'Instalado',
        'not_installed' => 'No instalado',
    ],

    'filters' => [
        'installation_status' => 'Estado de instalación',
        'all_plugins'         => 'Todos los plugins',
        'installed'           => 'Instalado',
        'not_installed'       => 'No instalado',
        'active_status'       => 'Estado activo',
        'author'              => 'Autor',
        'webkul'              => 'Webkul',
        'third_party'         => 'Terceros',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Instalar',
            'heading'     => 'Instalar plugin :name',
            'description' => "¿Está seguro de que desea instalar el plugin ':name'? Esto ejecutará migraciones y seeders.",
            'submit'      => 'Instalar plugin',
        ],
        'uninstall' => [
            'title'      => 'Desinstalar',
            'heading'    => 'Desinstalar plugin',
            'submit'     => 'Desinstalar plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin instalado correctamente',
            'body'  => "El plugin ':name' se ha instalado.",
        ],
        'installed-failed' => [
            'title' => 'Error en la instalación',
        ],
        'uninstalled' => [
            'title' => 'Plugin desinstalado correctamente',
            'body'  => "El plugin ':name' se ha desinstalado.",
        ],
        'uninstalled-failed' => [
            'title' => 'Error en la desinstalación',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Información del plugin',
            'dependencies' => 'Dependencias',
        ],
        'name'         => 'Nombre del plugin',
        'author'       => 'Autor',
        'version'      => 'Versión',
        'dependencies' => 'Plugins requeridos',
        'dependents'   => 'Plugins que dependen de este',
        'is_installed' => 'Estado de instalación',
        'license'      => 'Licencia',
        'summary'      => 'Descripción',

        'dependencies-repeater' => [
            'title'        => 'Plugins requeridos',
            'name'         => 'Nombre del plugin',
            'is_installed' => 'Instalado',
            'placeholder'  => 'No se requieren dependencias',
        ],

        'dependents-repeater' => [
            'title'        => 'Plugins que dependen de este',
            'name'         => 'Nombre del plugin',
            'is_installed' => 'Instalado',
            'placeholder'  => 'Sin dependientes',
        ],

    ],

    'names' => [
        'accounting'     => 'Contabilidad',
        'accounts'       => 'Cuentas',
        'analytics'      => 'Analíticas',
        'barcode'        => 'Código de barras',
        'blogs'          => 'Blogs',
        'chatter'        => 'Chatter',
        'contacts'       => 'Contactos',
        'employees'      => 'Empleados',
        'fields'         => 'Campos personalizados',
        'full-calendar'  => 'Calendario',
        'inventories'    => 'Inventario',
        'invoices'       => 'Facturas',
        'maintenance'    => 'Mantenimiento',
        'manufacturing'  => 'Fabricación',
        'partners'       => 'Socios',
        'payments'       => 'Pagos',
        'plugin-manager' => 'Gestor de plugins',
        'products'       => 'Productos',
        'projects'       => 'Proyectos',
        'purchases'      => 'Compras',
        'recruitments'   => 'Reclutamiento',
        'sales'          => 'Ventas',
        'security'       => 'Seguridad',
        'support'        => 'Soporte',
        'table-views'    => 'Vistas de tabla',
        'time-off'       => 'Ausencias',
        'timesheets'     => 'Partes de horas',
        'website'        => 'Sitio web',
    ],

    'summaries' => [
        'accounting'     => 'Gestionar plan de cuentas, diarios y asientos financieros',
        'accounts'       => 'Gestión de cuentas y configuración financiera',
        'analytics'      => 'Informes y paneles para información del negocio',
        'barcode'        => 'Aplicación de operaciones con código de barras para inventario y fabricación',
        'blogs'          => 'Gestionar blogs',
        'chatter'        => 'Registro de actividad, mensajería y seguimientos en los registros',
        'contacts'       => 'Gestión de contactos para clientes y proveedores',
        'employees'      => 'Gestión de empleados',
        'fields'         => 'Añadir campos personalizados a los recursos',
        'full-calendar'  => 'Vistas de calendario y programación de eventos',
        'inventories'    => 'Gestión de inventario y almacén',
        'invoices'       => 'Generación y gestión de facturas',
        'maintenance'    => 'Gestión de mantenimiento',
        'manufacturing'  => 'Gestión de fabricación y producción',
        'partners'       => 'Gestionar socios comerciales',
        'payments'       => 'Gestionar pagos y transacciones',
        'plugin-manager' => 'Gestor de plugins para Aureus ERP',
        'products'       => 'Catálogo de productos y gestión de variantes',
        'projects'       => 'Planificación y gestión de proyectos',
        'purchases'      => 'Gestión de adquisiciones y órdenes de compra',
        'recruitments'   => 'Seguimiento de candidatos y contratación',
        'sales'          => 'Gestión de embudo de ventas y oportunidades',
        'security'       => 'Roles, permisos y control de acceso',
        'support'        => 'Soporte al cliente y tickets',
        'table-views'    => 'Vistas de tabla guardadas y personalizables',
        'time-off'       => 'Gestión y seguimiento de ausencias',
        'timesheets'     => 'Seguimiento de horas de trabajo de empleados',
        'website'        => 'Sitio web para clientes',
    ],

];
