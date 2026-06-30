<?php

return [
    'global-search' => [
        'code' => 'Código',
        'type' => 'Tipo',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'Código',
                'account-name'           => 'Nombre de la cuenta',
                'accounting'             => 'Contabilidad',
                'account-type'           => 'Tipo de cuenta',
                'parent-account'         => 'Cuenta principal',
                'parent-account-helper'  => 'Seleccionar una cuenta existente para convertirla en subcuenta.',
                'default-taxes'          => 'Impuestos predeterminados',
                'tags'                   => 'Etiquetas',
                'journals'               => 'Diarios',
                'journals-helper'        => 'Sugeridos automáticamente según el tipo de cuenta seleccionado. Puede modificar la selección.',
                'currency'               => 'Moneda',
                'deprecated'             => 'Obsoleto',
                'reconcile'              => 'Permitir conciliación',
                'non-trade'              => 'No comercial',
                'companies'              => 'Empresas',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Código',
            'account-name'   => 'Nombre de la cuenta',
            'account-type'   => 'Cuenta',
            'parent-account' => 'Cuenta principal',
            'currency'       => 'Moneda',
            'journals'       => 'Diarios',
            'reconcile'      => 'Permitir conciliación',
        ],

        'grouping' => [
            'account-type' => 'Tipo de cuenta',
        ],

        'filters' => [
            'account-type'     => 'Tipo de cuenta',
            'parent-account'   => 'Cuenta principal',
            'allow-reconcile'  => 'Permitir conciliación',
            'currency'         => 'Moneda',
            'account-journals' => 'Diarios',
            'non-trade'        => 'No comercial',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Cuenta actualizada',
                    'body'  => 'La cuenta se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Cuenta eliminada',
                        'body'  => 'La cuenta se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar la cuenta',
                        'body'  => 'No se pudo eliminar la cuenta porque tiene apuntes contables asociados.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Cuentas eliminadas',
                        'body'  => 'Las cuentas se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar las cuentas',
                        'body'  => 'No se pudieron eliminar las cuentas porque tienen apuntes contables asociados.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Código',
                'account-name'   => 'Nombre de la cuenta',
                'accounting'     => 'Contabilidad',
                'account-type'   => 'Tipo de cuenta',
                'parent-account' => 'Cuenta principal',
                'sub-accounts'   => 'Subcuentas',
                'default-taxes'  => 'Impuestos predeterminados',
                'tags'           => 'Etiquetas',
                'journals'       => 'Diarios',
                'currency'       => 'Moneda',
                'deprecated'     => 'Obsoleto',
                'reconcile'      => 'Conciliación',
                'non-trade'      => 'No comercial',
            ],
        ],
    ],
];
