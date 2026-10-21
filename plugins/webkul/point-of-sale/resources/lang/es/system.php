<?php

return [
    'unexpected-error' => 'Algo salió mal en el servidor. Inténtelo de nuevo y avise a un responsable si sigue ocurriendo.',

    'products' => [
        'tip'      => 'Propinas',
        'discount' => 'Descuento',
    ],

    'picking' => [
        'operation-type-missing' => 'No hay ningún tipo de operación de stock configurado para el pedido :order, por lo que no se movió nada.',
    ],

    'config' => [
        'terminal-journal' => 'Punto de venta',
    ],

    'order-workflow' => [
        'customer' => [
            'required-by-terminal'       => 'Este punto de venta exige un cliente en cada pedido.',
            'required-to-invoice'        => 'Selecciona un cliente antes de facturar este pedido.',
            'required-to-ship'           => 'Selecciona un cliente antes de enviar este pedido más tarde.',
            'shipping-address-required'  => 'El cliente seleccionado necesita una dirección de entrega antes de enviar este pedido más tarde.',
            'required-by-payment-method' => 'El método de pago seleccionado exige un cliente.',
        ],

        'mark-paid' => [
            'already-settled'          => 'El pedido :order ya se ha liquidado.',
            'insufficient-payment'     => 'El pedido :order no está totalmente pagado.',
            'cash-method-required'     => 'El pedido :order no puede sobrepagarse porque esta terminal no tiene configurado ningún método de pago en efectivo.',
        ],

        'cancel' => [
            'not-draft' => 'El pedido :order ya no se puede cancelar.',
        ],

        'split' => [
            'not-draft' => 'El pedido :order ya no se puede dividir.',
        ],

        'tip' => [
            'not-enabled' => 'Activa las propinas y define un producto de propina en el punto de venta primero.',
        ],

        'refund' => [
            'not-refundable'    => 'El pedido :order no se puede reembolsar.',
            'exceeds-sold'      => 'La cantidad reembolsada supera lo que se vendió de :product.',
            'no-payment-method' => 'El terminal :order no tiene ningún método de pago con el que reembolsar.',
            'nothing-to-refund' => 'Selecciona al menos una línea para reembolsar.',
        ],
    ],

    'global-discount' => [
        'not-enabled' => 'Activa el descuento global y define un producto de descuento en el punto de venta primero.',
        'not-draft'   => 'Al pedido :order ya no se le puede aplicar descuento.',
    ],

    'terminal-product-creator' => [
        'name-required'    => 'Dale un nombre al producto.',
        'defaults-missing' => 'Configura una unidad de medida y una categoría de producto antes de crear productos en la caja.',
    ],

    'payment-method' => [
        'in-open-session' => 'Cierre las sesiones abiertas :sessions antes de eliminar este método de pago.',
    ],

    'payment-method-provisioner' => [
        'cash'         => 'Efectivo',
        'cash-journal' => 'Efectivo',
    ],

    'order-sync' => [
        'product-missing'         => 'Un producto de este pedido ya no existe. Elimínelo e inténtelo de nuevo.',
        'customer-missing'        => 'El cliente de este pedido ya no existe. Elija otro cliente.',
        'table-missing'           => 'La mesa de este pedido ya no existe. Mueva el pedido a otra mesa.',
        'price-list-missing'      => 'La lista de precios de este pedido ya no existe.',
        'fiscal-position-missing' => 'La posición fiscal de este pedido ya no existe.',
        'payment-method-missing'  => 'Un método de pago de este pedido ya no existe.',
        'refunded-order-missing'  => 'El pedido que se reembolsa ya no se encuentra.',
        'customer-email-invalid'  => 'La dirección de correo electrónico del cliente no es válida.',
        'discount-invalid'        => 'Los descuentos de línea deben estar entre 0 y 100%.',
    ],

    'order-processor' => [
        'price-locked'           => 'Cambiar el precio de :product requiere un responsable.',
        'line-discount-disabled' => 'Los descuentos por línea están desactivados en este punto de venta.',
        'foreign-order'          => 'Este pedido pertenece a otra caja y no se puede modificar aquí.',
        'foreign-line'           => 'Un artículo de este pedido pertenece a otro pedido. Recargue la caja e inténtelo de nuevo.',
    ],

    'floor-plan' => [
        'tables-in-use' => 'Las mesas :tables aún tienen pedidos abiertos. Cóbralos, libéralos o muévelos primero.',
    ],

    'session-preflight' => [
        'draft-orders' => 'Paga o cancela estos pedidos antes de cerrar la sesión: :orders.',

        'journal' => [
            'missing'      => 'Define un diario de ventas en el punto de venta.',
            'invalid-type' => 'El diario del punto de venta debe ser un diario de ventas.',
        ],

        'invoice-journal' => [
            'missing'      => 'Define un diario de facturas en el punto de venta.',
            'invalid-type' => 'El diario de facturas debe ser un diario de ventas.',
        ],

        'receivable-account' => [
            'missing'          => 'Define una cuenta a cobrar en el punto de venta.',
            'invalid-type'     => 'La cuenta a cobrar del punto de venta debe ser una cuenta a cobrar.',
            'not-reconcilable' => 'La cuenta a cobrar del punto de venta debe permitir la conciliación.',
            'deprecated'       => 'La cuenta a cobrar del punto de venta está obsoleta.',
        ],

        'payment-methods' => [
            'missing'               => 'Añade al menos un método de pago al punto de venta.',
            'pay-later-unsupported' => 'El método de pago :method no tiene diario y las cuentas de cliente aún no son compatibles.',
            'journal-missing'       => 'Define un diario en el método de pago :method.',
            'journal-company'       => 'El diario de :method pertenece a otra empresa.',
            'receivable-missing'    => 'Define una cuenta a cobrar para :method.',
        ],

        'cash-journal' => [
            'multiple'             => 'Solo se admite un método de pago en efectivo por punto de venta.',
            'profit-loss-missing'  => 'Define las cuentas de pérdidas y ganancias en el diario de efectivo :journal.',
        ],

        'taxes' => [
            'account-missing' => 'El impuesto :tax tiene una línea de distribución sin cuenta.',
        ],

        'global-discount' => [
            'product-missing' => 'Establece un producto de descuento en el punto de venta para usar descuentos globales.',
        ],

        'cogs' => [
            'stock-output-missing' => 'Define una cuenta de salida de stock antes de activar el coste de las ventas.',
        ],
    ],

    'invoice-payer' => [
        'entry-reference' => 'Pago de factura para :order (:invoice) mediante :method',
    ],

    'session-closer' => [
        'entry-reference'                 => 'Sesión del punto de venta :session',
        'payment-difference-reference'    => 'Diferencia en :method para :session',
        'cogs-reference'                  => 'Coste de las ventas de :session',
        'sales-line'                      => 'Ventas del punto de venta',
        'tax-line'                        => 'Impuestos del punto de venta',
        'receivable-line'                 => 'Cuenta a cobrar del punto de venta',
        'invoice-receivable-line'         => 'Cuenta a cobrar de facturas del punto de venta',
        'cash-line'                       => 'Efectivo del punto de venta',
        'cash-difference-line'            => 'Diferencia de efectivo al cierre',
        'cogs-line'                       => 'Coste de las ventas',
        'stock-output-line'               => 'Salida de stock',
        'balancing-line'                  => 'Diferencia al cierre',
        'rounding-line'                   => 'Redondeo de efectivo',
        'unbalanced'                      => 'El asiento de cierre está descuadrado en :delta. Elige una cuenta de ajuste para contabilizarlo.',
        'income-account-missing'          => 'No se ha resuelto ninguna cuenta de ingresos para :product.',
        'expense-account-missing'         => 'No se ha resuelto ninguna cuenta de gastos para :product.',
        'cash-account-missing'            => 'El diario de efectivo no tiene cuenta predeterminada.',
        'cash-difference-account-missing' => 'El diario de efectivo no tiene cuenta de pérdidas o ganancias.',
        'stock-output-missing'            => 'Define una cuenta de salida de stock en el punto de venta.',
        'line-account-missing'            => 'Una línea del asiento de cierre no tiene cuenta.',
        'rounding-account-missing'        => 'El redondeo de efectivo no tiene cuenta de pérdidas o ganancias.',
        'difference-exceeded'             => 'La diferencia máxima permitida es :amount. Contacta con tu responsable para aceptar la diferencia de cierre.',
    ],

    'invoicer' => [
        'customer-required' => 'El pedido :order necesita un cliente antes de poder facturarse.',
        'journal-missing'   => 'Define un diario de facturas en el punto de venta.',
        'session-closed'    => 'El pedido :order pertenece a una sesión cerrada y ya está contabilizado en el asiento de cierre.',
    ],

    'session-workflow' => [
        'open' => [
            'already-open' => 'Ya hay una sesión abierta para :config.',
            'inactive'     => 'El registro :config está inactivo y no se puede abrir.',
        ],

        'assert-open' => [
            'not-open' => 'La sesión :session no está abierta.',
        ],

        'assert-state' => [
            'invalid' => 'La sesión :session no puede pasar desde :state.',
        ],

        'discard' => [
            'not-discardable' => 'La sesión :name tiene actividad registrada y debe cerrarse, no descartarse.',
        ],

        'cash-movement' => [
            'invalid-amount' => 'El importe de un movimiento de efectivo debe ser mayor que cero.',
        ],
    ],
];
