<?php

return [
    'title' => 'Configuración de activos',

    'navigation' => [
        'title' => 'Activos y etiquetado',
        'group' => 'Activos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Configuración general de activos',
                'description' => 'Gestione la identificación de activos, secuencias de numeración y parámetros operativos.',
            ],
            'warehouses_and_operation' => [
                'title'       => 'Almacenes y modelos operativos',
                'description' => 'Configure almacenes predeterminados y gestione activos fijos asignados a sitios.',
            ],
            'custody_and_duration' => [
                'title'       => 'Reglas de custodia y duraciones',
                'description' => 'Defina períodos de custodia, fechas previstas de devolución y restricciones de asignación.',
            ],
            'governance_and_attachments' => [
                'title'       => 'Documentación y gobernanza de bajas',
                'description' => 'Exija adjuntos obligatorios para transiciones de custodia y gestione bajas de activos.',
            ],
        ],
        'fields' => [
            'tag-prefix'                          => 'Prefijo de etiqueta de activo',
            'auto-generate-tag'                   => 'Autogenerar etiqueta de activo',
            'tag-padding'                         => 'Relleno de dígitos de etiqueta',
            'default-condition'                   => 'Condición predeterminada del activo',
            'require-serial-by-default'           => 'Requerir número de serie por defecto',
            'default-warehouse'                   => 'Almacén predeterminado',
            'enable-site-assets'                  => 'Habilitar activos de sitio fijo',
            'require-site-on-custody'             => 'Requerir sitio al entregar custodia',
            'default-custody-duration-days'       => 'Duración predeterminada de custodia (días)',
            'require-expected-return-date'        => 'Requerir fecha prevista de devolución',
            'prevent-duplicate-product-custody'   => 'Evitar duplicados del mismo producto',
            'require-attachments-on-custody'      => 'Requerir adjuntos en entrega de custodia',
            'require-attachments-on-damage'       => 'Requerir adjuntos en devolución dañada',
            'require-attachments-on-termination'  => 'Requerir adjuntos al dar de baja',
            'restrict-termination-to-managers'    => 'Restringir bajas a gerentes',
            'termination-approval-cost-threshold' => 'Umbral de costo para aprobación de baja',
        ],
        'helpers' => [
            'tag-prefix'                          => 'Prefijo utilizado al generar automáticamente etiquetas de activos (ej. AST-).',
            'tag-prefix-locked'                   => 'El prefijo de etiqueta está bloqueado porque ya se han registrado activos.',
            'auto-generate-tag'                   => 'Asignar automáticamente etiquetas secuenciales a activos nuevos.',
            'tag-padding'                         => 'Número de dígitos para el número secuencial (3 a 8). Vista previa: :preview',
            'default-condition'                   => 'Condición inicial asignada a activos recién registrados.',
            'require-serial-by-default'           => 'Exigir números de serie obligatorios durante el registro de unidades individuales.',
            'default-warehouse'                   => 'Almacén predeterminado asignado a activos disponibles si no se especifica uno.',
            'manage-warehouses-link'              => 'Administrar almacenes',
            'enable-site-assets'                  => 'Permitir asignar activos directamente a sitios fijos sin un empleado asignado.',
            'manage-sites-link'                   => 'Administrar sitios',
            'require-site-on-custody'             => 'Exigir seleccionar un sitio operativo al entregar un activo a un empleado.',
            'default-custody-duration-days'       => 'Días agregados a la fecha de entrega para calcular la fecha prevista de devolución.',
            'require-expected-return-date'        => 'Exigir fecha prevista de devolución en todas las entregas a empleados.',
            'prevent-duplicate-product-custody'   => 'Evitar asignar más de un activo del mismo producto al mismo empleado a la vez.',
            'require-attachments-on-custody'      => 'Exigir recibos de entrega o comprobantes en emisiones y transferencias.',
            'require-attachments-on-damage'       => 'Exigir fotografías o evidencias al devolver un activo en mal estado o dañado.',
            'require-attachments-on-termination'  => 'Exigir documentación oficial al dar de baja o marcar un activo como perdido.',
            'restrict-termination-to-managers'    => 'Requerir permisos de gerente (Grupo o Global) para dar de baja o marcar como perdido.',
            'termination-approval-cost-threshold' => 'Los activos con costo inferior a este umbral pueden darse de baja sin aprobación del gerente. Dejar vacío para todos los montos.',
        ],
    ],

    'warnings' => [
        'site-assets-held' => 'No se pueden desactivar los activos de sitio mientras haya activos asignados a sitios fijos.',
    ],
];
