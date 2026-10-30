<?php

return [
    'model-label'        => 'Plan recurrente',
    'plural-model-label' => 'Planes recurrentes',

    'navigation' => [
        'title' => 'Planes recurrentes',
    ],

    'header-actions' => [
        'create' => 'Nuevo plan recurrente',
    ],

    'form' => [
        'name'             => 'Nombre del plan',
        'name-placeholder' => 'p. ej. Mensual',
        'months'           => 'Número de meses',
        'months-helper'    => 'Cuántos meses cubre el ingreso recurrente (Mensual = 1, Anual = 12).',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Plan',
            'months'        => 'Meses',
            'opportunities' => 'Oportunidades',
        ],
    ],

    'tabs' => [
        'active'   => 'Activos',
        'archived' => 'Archivados',
    ],

    'notifications' => [
        'created'  => 'Plan recurrente creado',
        'updated'  => 'Plan recurrente actualizado',
        'restored' => 'Plan recurrente restaurado',
        'deleted'  => 'Plan recurrente eliminado',
    ],

    'opportunity' => [
        'recurring-revenue' => 'Ingreso recurrente',
        'plan'              => 'Plan recurrente',
        'mrr'               => 'Ingreso recurrente mensual',
    ],
];
