<?php

return [
    'title' => 'Código de barras',

    'navigation' => [
        'back'        => 'Atrás',
        'home'        => 'Operaciones',
        'search'      => 'Buscar...',
        'label'       => 'Navegación',
        'open'        => 'Abrir navegación',
        'coming-soon' => 'Próximamente',
    ],

    'auth' => [
        'login-title'       => 'Inicio de sesión de Código de barras',
        'login-heading'     => 'Inicie sesión en Código de barras',
        'login-subheading'  => 'Continúe a la aplicación de operaciones de código de barras.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Código de barras',
            'label' => 'Aplicación de código de barras',
        ],
    ],

    'dashboard' => [
        'operations' => 'Operaciones',
        'empty'      => 'No hay operaciones disponibles.',
    ],

    'operation-search' => [
        'placeholder'    => 'Escanee o introduzca el código de barras de la operación...',
        'open'           => 'Abrir',
        'not-found'      => 'No se encontró ninguna operación activa para este código de barras.',
        'multiple-found' => 'Se encontraron :count operaciones coincidentes.',
    ],

    'transfers' => [
        'title' => 'Transferencias',
        'empty' => 'No se encontraron transferencias.',
    ],

    'adjustments' => [
        'title'             => 'Ajustes de inventario',
        'subtitle'          => 'Cuente las existencias por ubicación, producto o lote',
        'search'            => 'Escanee o busque por ubicación, producto, lote, serie...',
        'empty'             => 'No se encontraron cantidades de inventario.',
        'location-scanned'  => 'Escaneando la ubicación :location. Escanee más productos aquí o escanee otra ubicación.',
        'location-cleared'  => 'Se borraron los filtros de ajuste de inventario.',
        'product-not-found' => 'Este producto no está disponible en la selección de inventario actual.',
        'lot-not-found'     => 'Este lote o serie no está disponible en la selección de inventario actual.',
        'multiple-found'    => 'Se encontraron :count cantidades de inventario coincidentes.',
        'count-saved'       => 'Recuento de inventario guardado.',
        'count-applied'     => 'Ajuste de inventario aplicado.',
        'count-cleared'     => 'Recuento de inventario borrado.',
        'counted'           => 'Contado',
        'on-hand'           => 'Disponible',
        'location'          => 'Ubicación',
        'product'           => 'Producto',
        'lot-serial'        => 'Lote/Serie',
        'clear-filters'     => 'Borrar filtros',
        'apply'             => 'Aplicar',
        'clear'             => 'Borrar',
        'editor-title'      => 'Detalles del ajuste',
        'editor-subtitle'   => 'Revise los detalles de las existencias y actualice la cantidad contada.',
        'editor-image'      => 'Imagen de la cantidad de inventario',
        'edit-tooltip'      => 'Editar cantidad de inventario',
    ],

    'operation' => [
        'scan'                 => 'Escanee un producto, lote, paquete, empaquetado o transferencia',
        'manual-scan'          => 'Escanee o busque por producto, referencia, código de barras...',
        'search'               => 'Buscar producto, referencia, código de barras...',
        'moves'                => 'Movimientos',
        'source'               => 'Desde',
        'available'            => 'Disponible',
        'discard'              => 'Descartar',
        'confirm'              => 'Confirmar',
        'counted'              => 'Contado',
        'lot-serial'           => 'Número de lote / serie',
        'stock-title'          => 'Cantidad en existencias',
        'empty-moves'          => 'No se encontraron movimientos.',
        'details-title'        => 'Detalles del movimiento',
        'settings-title'       => 'Configuración del movimiento',
        'pick-from'            => 'Recoger de',
        'destination-location' => 'Ubicación de destino',
        'destination-package'  => 'Paquete de destino',
        'select-package'       => 'Seleccionar paquete',
        'stock-subtitle'       => 'Seleccione desde dónde más recoger el producto',
        'no-stock-locations'   => 'No se encontraron ubicaciones de existencias.',
        'camera-unavailable'   => 'Cámara no disponible',
        'submit-scan'          => 'Enviar escaneo',
        'image-alt'            => 'Imagen de la línea de movimiento',
        'edit-tooltip'         => 'Editar línea de movimiento',
    ],

    'scan' => [
        'empty'                    => 'Introduzca o escanee un código de barras.',
        'not-found'                => 'No se encontró ningún código de barras coincidente.',
        'operation-matched'        => 'Transferencia coincidente.',
        'product-not-on-operation' => 'Este producto no forma parte de la operación.',
        'package-matched'          => 'Paquete coincidente.',
        'move-located'             => 'Movimiento localizado. Introduzca la cantidad contada.',
        'move-updated'             => 'Cantidad del movimiento actualizada.',
        'move-counted'             => 'Movimiento marcado como contado.',
    ],

    'actions' => [
        'confirm'                  => 'Confirmar',
        'confirm-prompt'           => '¿Está seguro de que desea',
        'cancel'                   => 'Cancelar',
        'check-availability'       => 'Comprobar disponibilidad',
        'validate'                 => 'Validar',
        'return'                   => 'Devolver',
        'stay-on-transfer'         => 'Descartar',
        'no-backorder'             => 'Sin pedido pendiente',
        'backorder-title'          => 'Transferencia incompleta',
        'backorder-prompt'         => 'Si valida ahora, los productos restantes se añadirán a un pedido pendiente.',
        'backorder-col-product'    => 'Producto',
        'backorder-col-done-todo'  => 'Hecho / Por hacer',
        'backorder-col-backorder'  => 'Pedido pendiente',
        'completed'                => 'Acción completada.',
        'unsupported'              => 'Acción de código de barras no admitida.',
        'no-moves'                 => 'Esta operación no tiene movimientos.',
        'no-return-quantities'     => 'No hay cantidades para devolver.',
    ],
];
