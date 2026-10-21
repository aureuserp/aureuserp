<?php

return [
    'common' => [
        'ok'      => 'Aceptar',
        'close'   => 'Cerrar',
        'cancel'  => 'Cancelar',
        'save'    => 'Guardar',
        'saving'  => 'Guardando…',
        'discard' => 'Descartar',
        'clear'   => 'Limpiar',
        'apply'   => 'Aplicar',
        'open'    => 'Abrir',
        'resume'  => 'Reanudar',
        'confirm' => 'Confirmar',
        'back'    => 'Atrás',
        'print'   => 'Imprimir',
        'new'     => 'Nuevo',
        'more'    => '+ :count más',
    ],

    'nav' => [
        'home'      => 'Inicio',
        'orders'    => 'Pedidos',
        'registers' => 'Cajas',
    ],

    'picker' => [
        'title'       => 'Cajas',
        'open'        => 'Abrir caja',
        'resume'      => 'Reanudar',
        'discard'     => 'Descartar sesión',
        'open-by'     => 'Abierta por :name',
        'closed'      => 'Cerrada',
        'opening'     => 'Abriendo…',
        'back-office' => 'Oficina administrativa',
        'logout'      => 'Cerrar sesión',
        'offline'     => 'Conéctate a la red para abrir una caja.',
        'failed'      => 'No se pudo contactar con el servidor. Inténtalo de nuevo.',

        'discard-confirm' => [
            'heading' => '¿Descartar esta sesión?',
            'confirm' => 'Descartar',
        ],

        'empty' => [
            'heading'     => 'No hay ningún punto de venta disponible',
            'description' => 'Crea un punto de venta activo en la oficina administrativa para empezar a vender.',
        ],
    ],

    'history' => [
        'heading'       => 'Pedidos',
        'back'          => 'Atrás',
        'search'        => 'Buscar por pedido, recibo, cliente o mesa',
        'empty'         => 'Ningún pedido coincide con esa búsqueda.',
        'select-order'  => 'Selecciona un pedido para ver sus detalles.',
        'walk-in'       => 'Sin cita',
        'invoice'       => 'Factura',
        'invoiced'      => 'Facturado',
        'print-invoice' => 'Imprimir factura',
        'cancel'        => 'Cancelar pedido',
        'print'         => 'Imprimir recibo',
        'details'       => 'Detalles',
        'refund'        => 'Reembolso',
        'refund-prompt' => 'Selecciona el o los productos a reembolsar y establece la cantidad.',
        'to-refund'     => 'A reembolsar',
        'refundable'    => 'Reembolsable: :qty',
        'taxes'         => 'Impuestos',
        'total'         => 'Total',
        'paid'          => 'Pagado',
        'lines'         => 'Líneas',
        'payments'      => 'Pagos',
        'prev'          => 'Anterior',
        'next'          => 'Siguiente',
        'page'          => 'Página :page de :total',
        'unreachable'   => 'No se pudo contactar con el servidor. Comprueba la conexión e inténtalo de nuevo.',
        'failed'        => 'La solicitud falló (:status).',

        'invoice-customer' => [
            'heading'        => 'Se necesita un cliente para facturar',
            'question'       => '¿Deseas abrir la lista de clientes para seleccionar uno?',
            'select-heading' => 'Selecciona un cliente para facturar',
        ],

        'columns' => [
            'date'     => 'Fecha',
            'receipt'  => 'Recibo',
            'order'    => 'Pedido',
            'customer' => 'Cliente',
            'cashier'  => 'Cajero',
            'tracking' => 'Seguimiento',
            'table'    => 'Mesa',
            'total'    => 'Total',
            'status'   => 'Estado',
        ],

        'status' => [
            'active'   => 'Activo',
            'draft'    => 'En curso',
            'paid'     => 'Pagado',
            'done'     => 'Contabilizado',
            'invoiced' => 'Facturado',
            'canceled' => 'Cancelado',
        ],
    ],

    'parked' => [
        'walk-in'         => 'Sin registrar',
        'items'           => '{1} :count artículo|[2,*] :count artículos',
        'count'           => ':count aparcados',
        'view-all'        => 'Ver todo',
        'heading'         => 'Pedidos aparcados',
        'search'          => 'Buscar pedidos por nombre, referencia o producto',
        'no-match'        => 'Ningún pedido coincide con esa búsqueda.',
        'parked-ago'      => 'aparcado :time',
        'discard'         => 'Descartar pedido',
        'discard-confirm' => [
            'heading'     => '¿Descartar el pedido aparcado?',
            'description' => 'Se perderán sus líneas. Esta acción no se puede deshacer.',
        ],

        'empty' => [
            'heading'     => 'Nada aparcado',
            'description' => 'Los pedidos que dejes a un lado esperarán aquí.',
        ],
    ],

    'opening-control' => [
        'heading'                  => 'Control de apertura',
        'cash'                     => 'Efectivo de apertura',
        'previous-closing-balance' => 'Saldo de cierre de la sesión anterior: :amount',
        'note'                     => 'Nota de apertura',
        'placeholder'              => 'Añade una nota de apertura…',
        'confirm'                  => 'Abrir caja',
        'cancel'                   => 'Cancelar',
    ],

    'tabs' => [
        'new'      => 'Nuevo pedido',
        'all'      => 'Todos los pedidos',
        'view-all' => 'Ver todo',
    ],

    'menu' => [
        'label'          => 'Menú',
        'orders'         => 'Pedidos',
        'cash-in-out'    => 'Entrada / Salida de efectivo',
        'create-product' => 'Crear producto',
        'back-office'    => 'Back office',
        'close-register' => 'Cerrar caja',
        'install'        => 'Instalar aplicación',
        'theme'          => 'Cambiar tema',
        'logout'         => 'Cerrar sesión',
    ],

    'cash-movement' => [
        'heading' => 'Entrada / salida de efectivo',
        'in'      => 'Entrada de efectivo',
        'out'     => 'Salida de efectivo',
        'amount'  => 'Importe',
        'reason'  => 'Motivo',
        'confirm' => 'Registrar movimiento',
        'close'   => 'Cerrar',

        'notification' => [
            'title' => 'Movimiento de efectivo registrado',

            'body' => [
                'in'  => ':amount añadidos a la caja registradora.',
                'out' => ':amount retirados de la caja registradora.',
            ],
        ],
    ],

    'closing' => [
        'copy'                     => 'Copiar el importe esperado',
        'heading'                  => 'Cerrando la caja',
        'expected'                 => 'Esperado en el cajón',
        'counted'                  => 'Contado',
        'note'                     => 'Nota de cierre',
        'confirm'                  => 'Cerrar caja',
        'back'                     => 'Volver al TPV',
        'method'                   => 'Método de pago',
        'total'                    => 'Total',
        'opening'                  => 'Apertura',
        'payments'                 => 'Pagos en efectivo',
        'moves'                    => 'Entrada / salida de efectivo',
        'cash-in'                  => 'Entrada de efectivo :number',
        'cash-out'                 => 'Salida de efectivo :number',
        'orders'                   => ':quantity pedidos',
        'difference'               => 'Diferencia',
        'count'                    => 'Recuento de efectivo',
        'clear'                    => 'Limpiar',
        'discard'                  => 'Descartar',
        'daily-sale'               => 'Venta diaria',
        'opening-note'             => 'Nota de apertura',
        'authorized-diff'          => 'Diferencia máxima permitida: :amount',
        'authorized-diff-exceeded' => 'La diferencia supera el límite permitido. Solo un responsable puede cerrar esta caja.',
        'unbalanced'               => 'El asiento de cierre tiene un descuadre de :amount. Un gerente debe seleccionar una cuenta de compensación para cerrar.',
    ],

    'draft-orders' => [
        'heading'       => 'Error',
        'message'       => 'No puedes cerrar el PdV mientras haya pedidos en borrador.',
        'review-orders' => 'Revisar pedidos',
        'cancel-orders' => 'Cancelar pedidos',
    ],

    'product-form' => [
        'heading'             => 'Nuevo producto',
        'name'                => 'Nombre del producto',
        'name-placeholder'    => 'p. ej. Hamburguesa con queso',
        'barcode'             => 'Código de barras',
        'barcode-placeholder' => 'p. ej. 1234567890',
        'tracking'            => 'Seguir inventario',
        'price'               => 'Precio de venta',
        'taxes'               => 'Impuestos de venta',
        'tax-included'        => '(= :amount impuestos incluidos)',
        'category'            => 'Categoría del TPV',
        'unsaleable'          => 'No vendible',

        'notification' => [
            'title' => 'Producto creado',
        ],

        'error' => [
            'failed'  => 'No se pudo crear el producto (:status)',
            'offline' => 'Crear un producto requiere conexión.',
        ],
    ],

    'product-info' => [
        'heading'          => 'Información del producto',
        'inventory'        => 'Inventario',
        'available'        => 'disponibles en esta caja',
        'negative-warning' => 'La venta sigue permitida; el stock quedará en negativo y el back office mostrará el faltante.',
        'financials'       => 'Finanzas',
        'price'            => 'Precio',
        'cost'             => 'Coste',
        'margin'           => 'Margen',
        'order'            => 'En este pedido',
        'quantity'         => 'Cantidad',
        'total-price'      => 'Precio total',
        'total-margin'     => 'Margen total',
        'add'              => 'Añadir al pedido',
    ],

    'customers' => [
        'heading'      => 'Seleccionar un cliente',
        'search'       => 'Buscar clientes',
        'no-match'     => 'Ningún cliente coincide con esa búsqueda. Solo se pueden buscar sin conexión los clientes cargados al iniciar la sesión.',
        'clear'        => 'Quitar cliente',
        'badge-new'    => 'nuevo',

        'create' => [
            'label'   => 'Nuevo cliente',
            'heading' => 'Nuevo cliente',
            'created' => ':name añadido y seleccionado.',

            'fields' => [
                'name'  => 'Nombre',
                'email' => 'Correo electrónico',
                'phone' => 'Teléfono',
            ],
        ],
    ],

    'variants' => [
        'heading'     => 'Selección de atributos',
        'confirm'     => 'Añadir',
        'unavailable' => 'Esta combinación no existe.',
    ],

    'lots' => [
        'heading'        => 'Número(s) de lote/serie requerido(s)',
        'placeholder'    => 'Número de serie/lote',
        'add'            => 'Añadir',
        'remove'         => 'Quitar número',
        'missing'        => 'Indicar número de lote / serie',
        'none-available' => 'No hay número de serie/lote para el producto seleccionado y su creación no está permitida desde la aplicación del Punto de venta.',

        'warning' => [
            'heading' => 'Faltan algunos números de serie/lote',
            'body'    => "Está intentando vender productos con números de serie/lote, pero algunos no están definidos.\n¿Desea continuar de todas formas?",
            'proceed' => 'Aceptar',
        ],
    ],

    'notes' => [
        'internal'    => 'Nota interna',
        'kitchen'     => 'Nota de cocina',
        'heading'     => 'Añadir nota interna',
        'empty'       => 'No hay modelos de nota configurados.',
        'hint'        => 'Elige primero una línea y luego una nota.',
        'placeholder' => 'Añade una nota para esta línea',
    ],

    'money-details' => [
        'label'            => 'Monedas/Billetes',
        'opening-heading'  => 'Detalles de apertura:',
        'closing-heading'  => 'Detalles de cierre:',
        'total'            => 'Total: :total',
        'confirm'          => 'Confirmar',
        'close'            => 'Cerrar',
        'increase'         => 'Añadir uno',
        'decrease'         => 'Quitar uno',
    ],

    'cart' => [
        'discount' => ':percentage% de descuento',
        'subtotal' => 'Subtotal',
        'tax'      => 'Impuestos',
        'rounding' => 'Redondeo',
        'total'    => 'Total',
        'remove'   => 'Quitar :product',

        'empty' => [
            'description' => 'Escanea o toca un producto para empezar',
        ],
    ],

    'catalogue' => [
        'search'          => 'Buscar productos',
        'create-product'  => 'Crear producto',
        'info'            => 'Información del producto :product',
        'info-depleted'   => 'Información del producto :product, sin unidades disponibles',
    ],

    'numpad' => [
        'qty'       => 'Cant.',
        'price'     => 'Precio',
        'backspace' => 'Retroceso',
        'clear'     => 'C',
    ],

    'payment' => [
        'ship-later'                            => 'Enviar más tarde',
        'ship-later-heading'                    => 'Seleccione la fecha de envío',
        'ship-later-error-heading'              => 'Dirección incorrecta para el envío',
        'ship-later-no-customer'                => 'Selecciona un cliente antes de enviar este pedido más tarde.',
        'ship-later-no-address'                 => 'El cliente seleccionado necesita una dirección.',
        'select-method'                         => 'Seleccione un método de pago',
        'invoice'                               => 'Factura',
        'change'                                => 'cambio',
        'remove'                                => 'Quitar el pago de :method',
        'validate'                              => 'Validar',
        'max-value-title'                       => 'Valor máximo alcanzado',
        'max-value-body'                        => 'El importe no puede ser mayor que el importe adeudado si no tienes configurado un método de pago en efectivo.',
        'customer-required-title'               => 'Cliente requerido',
        'customer-required-body-terminal'       => 'Selecciona un cliente antes de validar este pedido; esta terminal lo requiere.',
        'customer-required-body-payment-method' => 'Selecciona un cliente antes de validar este pedido; el método de pago seleccionado lo requiere.',
        'customer-required-body-invoice'        => 'Selecciona un cliente antes de validar este pedido para generar una factura.',
        'incomplete-title'                      => 'Método de pago requerido',
        'incomplete-body'                       => 'Seleccione un método de pago que cubra el total antes de validar este pedido.',
    ],

    'floor' => [
        'back'      => 'Plano',
        'table'     => 'Mesa :table',
        'no-table'  => 'Sin mesa',
        'empty'     => 'Aún no hay mesas en esta planta. Añádelas desde la administración.',
        'no-floors' => 'No hay plantas disponibles. Añada una planta nueva para empezar.',
        'seats'     => '{1} :count asiento|[2,*] :count asientos',
        'orders'    => '{1} :count pedido|[2,*] :count pedidos',
        'guests'    => '{1} :count comensal|[2,*] :count comensales',
    ],

    'guests' => [
        'label'     => 'Comensales',
        'heading'   => 'Número de comensales',
        'increase'  => 'Añadir comensal',
        'decrease'  => 'Quitar comensal',
        'per-guest' => ':amount por comensal',
    ],

    'split' => [
        'label'    => 'Dividir',
        'heading'  => 'Dividir la cuenta',
        'hint'     => 'Toca una línea para pasar una unidad a la nueva cuenta. Toca de nuevo para pasar más.',
        'new-bill' => 'Nueva cuenta',
        'confirm'  => 'Dividir pedido',
    ],

    'bill' => [
        'label'   => 'Cuenta',
        'heading' => 'Imprimir la cuenta',
    ],

    'takeaway' => [
        'badge'       => 'Para llevar',
        'to-takeaway' => 'Cambiar a para llevar',
        'to-dine-in'  => 'Cambiar a comer aquí',
    ],

    'order-name' => [
        'label'       => 'Editar nombre del pedido',
        'heading'     => 'Editar nombre del pedido',
        'placeholder' => 'p. ej. 18:45 Juan 4P',
    ],

    'tip' => [
        'label'  => 'Propina',
        'add'    => 'Añadir propina',
        'change' => 'Cambiar propina',
        'remove' => 'Quitar propina',
    ],

    'global-discount' => [
        'label'   => 'Descuento',
        'heading' => 'Porcentaje de descuento',
        'apply'   => 'Aplicar',
        'hint'    => 'Sustituye cualquier descuento ya aplicado al pedido.',
    ],

    'booking' => [
        'book'    => 'Reservar mesa',
        'release' => 'Liberar mesa',
    ],

    'transfer' => [
        'label'        => 'Transferir / Fusionar',
        'prompt'       => 'Selecciona una mesa para transferir :order',
        'has-payments' => 'Este pedido tiene pagos. Elimínalos antes de fusionarlo con otra mesa.',
    ],

    'table-selector' => [
        'label'       => 'Mesa',
        'heading'     => 'Selector de mesa',
        'hint'        => 'Introduce un número de mesa o un nombre para un pedido sin mesa.',
        'placeholder' => 'Número de mesa o nombre',
        'jump'        => 'Ir',
    ],

    'session-closed' => [
        'heading' => 'Caja cerrada',
        'body'    => 'Esta caja se cerró desde otra ventana o dispositivo. Los pedidos abiertos en este dispositivo no se enviaron; vuelve a abrir la caja para continuar.',
        'back'    => 'Volver a las cajas',
    ],

    'floor-plan' => [
        'edit'                 => 'Editar plano',
        'switch-view'          => 'Cambiar vista de planta',
        'view-map'             => 'Vista de mapa',
        'view-grid'            => 'Vista de cuadrícula',
        'floor-name'           => 'Nombre de la planta',
        'background'           => 'Fondo de la planta',
        'no-colour'            => 'Sin color',
        'add-table'            => 'Añadir mesa',
        'add-floor'            => 'Añadir planta',
        'delete-floor'         => 'Eliminar planta',
        'confirm-delete-floor' => 'Confirmar eliminación',
        'table-number'         => 'Mesa',
        'fewer-seats'          => 'Menos asientos',
        'more-seats'           => 'Más asientos',
        'make-square'          => 'Hacer cuadrada',
        'make-round'           => 'Hacer redonda',
        'duplicate'            => 'Duplicar',
        'delete-table'         => 'Eliminar mesa',
        'hint'                 => 'Arrastra las mesas para moverlas, arrastra la esquina para cambiar su tamaño y toca una mesa para editarla.',
        'new-floor'            => 'Planta :number',
        'failed'               => 'No se pudo guardar el plano (:status).',
        'add-image'            => 'Añadir imagen',
        'change-image'         => 'Cambiar imagen',
        'remove-image'         => 'Quitar imagen',
        'zoom-in'              => 'Acercar',
        'zoom-out'             => 'Alejar',
        'fit'                  => 'Ajustar a la pantalla',
        'unreachable'          => 'No se pudo contactar con el servidor. Compruebe la conexión e inténtelo de nuevo.',

        'colours' => [
            'red'        => 'Rojo',
            'orange'     => 'Naranja',
            'yellow'     => 'Amarillo',
            'green'      => 'Verde',
            'teal'       => 'Verde azulado',
            'blue'       => 'Azul',
            'violet'     => 'Violeta',
            'pink'       => 'Rosa',
            'stone'      => 'Piedra',
            'white'      => 'Blanco',
            'light-grey' => 'Gris claro',
            'cream'      => 'Crema',
            'mint'       => 'Menta',
            'sky'        => 'Celeste',
            'lavender'   => 'Lavanda',
            'blush'      => 'Rosa pálido',
            'warm-grey'  => 'Gris cálido',
        ],
    ],

    'receipt' => [
        'phone'     => 'Tel.:',
        'served-by' => 'Atendido por :cashier',
        'untaxed'   => 'Base imponible',
        'rounding'  => 'Redondeo',
        'total'     => 'TOTAL',
        'change'    => 'Cambio',
        'order'     => 'Pedido :order',
        'new-order' => 'Nuevo pedido',
        'guests'    => '{1} :count comensal|[2,*] :count comensales',
    ],

    'install' => [
        'installed'   => 'Esta caja ya está instalada como aplicación en este dispositivo.',
        'unavailable' => 'Este navegador no puede instalar la caja. Se requiere Chrome o Edge con HTTPS.',
        'label'       => 'Instalar la app',
    ],

    'scanner' => [
        'unsupported'   => 'Este navegador no puede escanear con la cámara. Usa un lector de códigos de barras conectado.',
        'hardware-hint' => 'Un lector de códigos de barras conectado funciona en todo el TPV sin abrir esto.',
        'heading'       => 'Escanear un código de barras',
        'start'         => 'Escanear con la cámara',
        'stop'          => 'Detener',
    ],

    'drafts' => [
        'not-shared' => 'Este pedido no se comparte con las otras cajas',
        'rejected'   => 'El servidor rechazó este pedido.',
    ],

    'kitchen' => [
        'order'        => 'Pedido',
        'new'          => 'Nuevo',
        'cancelled'    => 'Cancelado',
        'note-changed' => 'Nota cambiada',
        'dine-in'      => 'Para comer aquí',
        'takeaway'     => 'Para llevar',
        'to-takeaway'  => 'Comer aquí → Para llevar',
        'to-dine-in'   => 'Para llevar → Comer aquí',
        'by'           => 'Por :name',
        'table'        => 'Mesa :table',
        'order-note'   => 'Nota del pedido',
        'offline'      => 'Enviar a cocina requiere conexión.',
        'nothing'      => 'No hay nada nuevo para enviar a cocina.',
    ],

    'rejected' => [
        'settled-heading' => 'Pagado en otra caja',
        'failed-heading'  => 'Este pedido no se guardó',
        'order'           => 'Pedido :order',
        'table'           => 'Mesa :table',
        'settled-body'    => ':order ya se pagó en otra caja. Esta venta no se registró.',
        'give-back'       => 'Devuelva :amount al cliente.',
        'failed-body'     => 'No se pudo guardar :order en el servidor.',
        'returned'        => 'Dinero devuelto',
        'dismiss'         => 'Descartar',
    ],

    'offline' => [
        'banner'              => 'Sin conexión — las ventas continúan y se sincronizan cuando vuelva la conexión',
        'waiting'             => ':count pedido(s) pendientes de sincronizar',
        'rejected'            => ':count pedido(s) rechazados por el servidor',
        'storage-unavailable' => 'Almacenamiento local no disponible — recarga antes de tomar más pedidos',
        'retry'               => 'Reintentar',
        'lost-heading'        => 'Conexión perdida',
        'lost-body'           => 'Hasta que se restablezca la conexión, el punto de venta funcionará con funcionalidad limitada.',
        'continue'            => 'Continuar con funcionalidad limitada',
    ],

    'actions' => [
        'customer' => 'Cliente',
        'note'     => 'Nota',
        'payment'  => 'Pago',
        'heading'  => 'Acciones',
        'label'    => 'Acciones',

        'cancel-order' => [
            'label'   => 'Cancelar pedido',
            'confirm' => 'Confirmar',
            'hint'    => 'Se perderán sus líneas. Esta acción no se puede deshacer.',
            'failed'  => 'No se pudo cancelar el pedido (:status).',
            'offline' => 'Cancelar este pedido requiere conexión.',
        ],
    ],

    'price-lists' => [
        'label'   => 'Lista de precios',
        'heading' => 'Selecciona la lista de precios',
        'default' => 'Precio predeterminado',
    ],

    'notification' => [
        'success' => [
            'title' => 'Pedido completado',
            'body'  => 'El pedido :order se ha liquidado.',
        ],

        'queued' => [
            'title' => 'Pedido en cola sin conexión',
            'body'  => ':count pedido(s) se sincronizarán cuando vuelva la conexión.',
        ],
    ],
];
