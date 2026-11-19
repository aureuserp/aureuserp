<?php

return [
    'title'        => 'Activo de la Empresa',
    'plural-title' => 'Activos de la Empresa',

    'navigation' => [
        'title' => 'Activos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalles del Activo',
                'fields' => [
                    'name'                       => 'Nombre del Activo',
                    'product'                    => 'Producto / Modelo',
                    'product-type'               => 'Tipo de Producto',
                    'quantity'                   => 'Cantidad',
                    'quantity-help'              => 'Cantidad total recibida en este lote.',
                    'asset-tag'                  => 'Código del Activo',
                    'asset-tag-help'             => 'Código identificador único para el activo dentro de la empresa.',
                    'asset-tag-auto-placeholder' => 'AST-XXXXX (Generado automáticamente)',
                    'serial-no'                  => 'Número de Serie',
                    'model'                      => 'Modelo',
                    'category'                   => 'Categoría',
                    'condition'                  => 'Condición',
                    'cost'                       => 'Costo',
                    'purchase-date'              => 'Fecha de Compra',
                    'warranty-until'             => 'Garantía Hasta',
                    'warehouse'                  => 'Almacén',
                    'warehouse-help'             => 'Almacén de inventario donde se guarda el activo mientras está disponible.',
                    'initial-placement'          => 'Ubicación inicial',
                    'placements'                 => [
                        'warehouse'        => 'Almacén de inventario',
                        'site-asset'       => 'Activo de sitio fijo',
                        'employee-custody' => 'Custodia de empleado',
                    ],
                    'current-site'               => 'Sitio',
                    'current-employee'           => 'Empleado',
                    'note'                       => 'Notas',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'asset-tag'        => 'Código del Activo',
            'name'             => 'Nombre del Activo',
            'product'          => 'Producto',
            'product-type'     => 'Tipo de Producto',
            'quantity'         => 'Cantidad',
            'model'            => 'Modelo',
            'serial-no'        => 'Nº de Serie',
            'category'         => 'Categoría',
            'status'           => 'Estado',
            'condition'        => 'Condición',
            'holder'           => 'Custodia Actual',
            'warehouse'        => 'Almacén',
            'current-employee' => 'Empleado Actual',
            'current-site'     => 'Ubicación Actual',
            'cost'             => 'Costo',
            'company'          => 'Empresa',
            'deleted-at'       => 'Archivado el',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'groups' => [
            'product'          => 'Producto / Modelo',
            'category'         => 'Categoría',
            'status'           => 'Estado',
            'condition'        => 'Condición',
            'warehouse'        => 'Almacén',
            'current-employee' => 'Empleado Actual',
            'current-site'     => 'Ubicación Actual',
        ],

        'filters' => [
            'status'              => 'Estado',
            'condition'           => 'Condición',
            'category'            => 'Categoría',
            'current-holder-type' => 'Tipo de Custodia',
            'current-employee'    => 'Empleado Actual',
            'department'          => 'Departamento',
            'warehouse'           => 'Almacén',
            'current-site'        => 'Ubicación Actual',
            'site-usage-type'     => 'Uso del sitio',
            'site-type'           => 'Tipo de sitio',
            'product'             => 'Producto',
            'product-type'        => 'Tipo de Producto',
            'company'             => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Activo actualizado',
                    'body'  => 'El activo ha sido actualizado con éxito.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'No se pudo eliminar el activo',
                    ],
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'actions' => [
        'issue' => [
            'label' => 'Entregar Custodia',
            'modal' => [
                'heading'                => 'Entregar Custodia de Activo',
                'description'            => 'Asignar este activo a un empleado o ubicación.',
                'consumable-description' => 'Saldo disponible: :quantity unidades. Las cantidades se descuentan de este lote al emitir.',
                'submit'                 => 'Confirmar Entrega',
            ],
            'fields' => [
                'allocation-mode'    => 'Modo de asignación',
                'allocation-modes'   => [
                    'employee-custody' => 'Custodia de empleado',
                    'site-asset'       => 'Activo de sitio fijo',
                ],
                'holder-type'        => 'Tipo de Custodia',
                'employee'           => 'Empleado',
                'employees'          => 'Destinatarios',
                'departments'        => 'Departamentos',
                'departments-help'   => 'Selección masiva de destinatarios por departamento. Elegir un sitio o departamentos completa la lista automáticamente.',
                'recipients-summary' => ':count destinatario(s) — :total unidad(es) en total',
                'site'               => 'Ubicación',
                'receiver'           => 'Receptor en Campo',
                'condition-out'      => 'Condición de Salida',
                'quantity'           => 'Cantidad por Destinatario',
                'quantity-help'      => 'Cada destinatario seleccionado recibe esta cantidad. El saldo del lote se reduce en consecuencia.',
                'issued-at'          => 'Fecha y Hora de Entrega',
                'expected-return-at' => 'Retorno Esperado',
                'issue-note'         => 'Nota de Entrega',
                'issue-attachments'  => 'Adjuntos de entrega',
            ],
            'notification' => [
                'title' => 'Custodia Entregada',
                'body'  => 'La custodia del activo ha sido entregada con éxito.',
            ],
        ],

        'transfer' => [
            'label' => 'Transferir Custodia',
            'modal' => [
                'heading'     => 'Transferir Custodia de Activo',
                'description' => 'Transferir este activo a un nuevo empleado o ubicación.',
                'submit'      => 'Confirmar Transferencia',
            ],
            'fields' => [
                'allocation-mode'    => 'Modo de asignación',
                'allocation-modes'   => [
                    'employee-custody' => 'Custodia de empleado',
                    'site-asset'       => 'Activo de sitio fijo',
                ],
                'holder-type'        => 'Nuevo Tipo de Custodia',
                'employee'           => 'Nuevo Empleado',
                'site'               => 'Nueva Ubicación',
                'receiver'           => 'Receptor en Campo',
                'condition-in'       => 'Condición del Poseedor Anterior',
                'issued-at'          => 'Fecha y Hora de Transferencia',
                'expected-return-at' => 'Retorno Esperado',
                'issue-note'         => 'Nota de Transferencia',
                'return-note'        => 'Nota de Devolución del Poseedor Anterior',
                'return-attachments' => 'Adjuntos de recepción previa',
                'issue-attachments'  => 'Adjuntos de nueva entrega',
            ],
            'notification' => [
                'title' => 'Custodia Transferida',
                'body'  => 'La custodia del activo ha sido transferida con éxito.',
            ],
        ],

        'return' => [
            'label' => 'Devolver al Almacén',
            'modal' => [
                'heading'     => 'Devolver Activo al Inventario',
                'description' => 'Recibir y verificar el activo de vuelta al inventario disponible.',
                'submit'      => 'Confirmar Devolución',
            ],
            'fields' => [
                'condition-in'       => 'Condición de Entrada',
                'ended-at'           => 'Fecha y Hora de Devolución',
                'warehouse'          => 'Almacén receptor',
                'return-note'        => 'Nota de Devolución',
                'return-attachments' => 'Adjuntos de devolución',
            ],
            'notification' => [
                'title' => 'Activo Devuelto',
                'body'  => 'El activo ha sido devuelto al inventario disponible.',
            ],
        ],

        'scrap' => [
            'label' => 'Desechar Activo',
            'modal' => [
                'heading'     => 'Desechar Activo',
                'description' => 'Marcar este activo como desechado. Esto cierra cualquier custodia abierta.',
                'submit'      => 'Confirmar Desecho',
            ],
            'fields' => [
                'ended-at'    => 'Fecha y Hora de Desecho',
                'note'        => 'Motivo / Nota de Desecho',
                'attachments' => 'Adjuntos de desecho',
            ],
            'notification' => [
                'title' => 'Activo Desechado',
                'body'  => 'El activo ha sido marcado como desechado.',
            ],
        ],

        'mark-lost' => [
            'label' => 'Marcar como Perdido',
            'modal' => [
                'heading'     => 'Marcar Activo como Perdido',
                'description' => 'Marcar este activo como perdido. Esto cierra cualquier custodia abierta.',
                'submit'      => 'Confirmar Pérdida',
            ],
            'fields' => [
                'ended-at'    => 'Fecha y Hora del Reporte',
                'note'        => 'Detalles de la Pérdida',
                'attachments' => 'Adjuntos del reporte',
            ],
            'notification' => [
                'title' => 'Activo Marcado como Perdido',
                'body'  => 'El activo ha sido marcado como perdido.',
            ],
        ],

        'bulk-register' => [
            'label' => 'Registro Masivo de Unidades',
            'modal' => [
                'heading'     => 'Registrar Unidades de Activos Masivamente',
                'description' => 'Registrar múltiples unidades del mismo modelo en un solo lote.',
                'submit'      => 'Registrar Unidades',
            ],
            'sections' => [
                'shared'  => 'Detalles Comunes del Lote',
                'mode'    => 'Modo de Seguimiento y Entrada',
                'preview' => 'Vista Previa de Validación',
            ],
            'fields' => [
                'company'             => 'Empresa',
                'product'             => 'Producto / Modelo',
                'name'                => 'Nombre del Activo',
                'model'               => 'Modelo',
                'category'            => 'Categoría',
                'condition'           => 'Condición Inicial',
                'cost'                => 'Costo Unitario',
                'purchase-date'       => 'Fecha de Compra',
                'warranty-until'      => 'Garantía Hasta',
                'warehouse'           => 'Almacén',
                'warehouse-help'      => 'Almacén de inventario inicial para los activos disponibles registrados.',
                'initial-placement'   => 'Ubicación inicial',
                'placements'          => [
                    'warehouse'        => 'Almacén de inventario',
                    'site-asset'       => 'Activo de sitio fijo',
                    'employee-custody' => 'Custodia de empleado',
                ],
                'site'                => 'Sitio',
                'employee'            => 'Empleado',
                'attachments'         => 'Archivos adjuntos de entrega',
                'note'                => 'Nota del Lote',
                'tracking-mode'       => 'Modo de Seguimiento',
                'modes'               => [
                    'with-serials'    => 'Unidades con Serie (Rastreadas por Nº de Serie)',
                    'without-serials' => 'Unidades sin Serie (Sillas, herramientas, etc.)',
                ],
                'serials-raw'         => 'Lista de Series (una por línea o separadas por comas)',
                'serials-raw-help'    => 'Pegar series: Nº de serie por línea o CODIGO,Nº_SERIE.',
                'parse-to-table'      => 'Convertir a tabla',
                'items'               => 'Entradas de unidades',
                'serial-no'           => 'Número de serie',
                'asset-tag'           => 'Etiqueta del activo',
                'quantity'            => 'Cantidad a Generar',
                'tags-raw'            => 'Códigos de Activos Preimpresos',
                'tags-raw-help'       => 'Un código de activo por línea para unidades sin serie.',
                'unit-note'           => 'Nota de la unidad',
            ],
            'preview' => [
                'valid'      => ':count Unidades Válidas',
                'duplicates' => ':count Duplicados en la Lista',
                'collisions' => ':count Existentes en BD',
            ],
            'notification' => [
                'title' => 'Registro Masivo Exitoso',
                'body'  => 'Se han registrado :count unidades de activos con éxito.',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'identity' => [
                'title'   => 'Identidad del Activo',
                'entries' => [
                    'asset-tag' => 'Código del Activo',
                    'name'      => 'Nombre del Activo',
                    'product'   => 'Producto',
                    'model'     => 'Modelo',
                    'category'  => 'Categoría',
                    'serial-no' => 'Número de Serie',
                    'status'    => 'Estado',
                    'condition' => 'Condición',
                ],
            ],

            'custody' => [
                'title'   => 'Custodia Actual',
                'entries' => [
                    'current-employee'   => 'Empleado Actual',
                    'current-site'       => 'Sitio Actual',
                    'site-usage'         => 'Uso del Sitio',
                    'warehouse'          => 'Almacén',
                    'initial-quantity'   => 'Cantidad inicial',
                    'available-quantity' => 'Cantidad disponible',
                    'issued-at'          => 'Fecha de Entrega',
                    'expected-return-at' => 'Retorno Esperado',
                    'custody-note'       => 'Nota de Custodia',
                ],
            ],

            'acquisition' => [
                'title'   => 'Adquisición y Datos Financieros',
                'entries' => [
                    'company'        => 'Empresa',
                    'purchase-date'  => 'Fecha de Compra',
                    'warranty-until' => 'Garantía Hasta',
                    'cost'           => 'Costo',
                    'creator'        => 'Creado por',
                ],
            ],

            'notes' => [
                'title'   => 'Notas',
                'entries' => [
                    'note' => 'Notas',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoría',
                'entries' => [
                    'created-at' => 'Creado el',
                    'updated-at' => 'Actualizado el',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'assignments' => [
            'title'   => 'Historial de Custodia y Movimientos',
            'columns' => [
                'occurred-at'        => 'Fecha y Hora',
                'type'               => 'Tipo de Movimiento',
                'source-type'        => 'Origen',
                'from-display'       => 'Desde',
                'to-display'         => 'Hacia',
                'condition'          => 'Condición',
                'performed-by'       => 'Realizado por',
                'expected-return-at' => 'Retorno Esperado',
                'note'               => 'Nota',
                'attachments'        => 'Adjuntos',
            ],
            'filters' => [
                'type'        => 'Tipo de Movimiento',
                'source-type' => 'Origen',
            ],
            'view-modal' => [
                'heading'  => 'Detalles del Movimiento',
                'sections' => [
                    'details'     => 'Información del Movimiento',
                    'attachments' => 'Documentos y Fotos Adjuntos',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nuevo Activo',
                    'notification' => [
                        'title' => 'Activo creado',
                        'body'  => 'El activo ha sido creado con éxito.',
                    ],
                ],
            ],
            'tabs' => [
                'all'            => 'Todos',
                'available'      => 'Disponible',
                'consumables'    => 'Consumibles',
                'site-assets'    => 'Activos de sitio fijo',
                'with-employees' => 'En Empleados',
                'at-sites'       => 'En Sitios / Almacenes',
                'in-the-field'   => 'En el Campo',
                'overdue-return' => 'Retorno Vencido',
                'scrapped-lost'  => 'Desechado / Perdido',
                'archived'       => 'Archivado',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Activo creado',
                'body'  => 'El activo ha sido creado con éxito.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Activo actualizado',
                'body'  => 'El activo ha sido actualizado con éxito.',
            ],
        ],
    ],
];
