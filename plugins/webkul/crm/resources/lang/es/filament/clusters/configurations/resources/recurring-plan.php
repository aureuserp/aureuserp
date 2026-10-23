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

    'actions' => [
        'archive'             => 'Archivar',
        'archive-heading'     => 'Archivar plan recurrente',
        'archive-description' => 'El plan pasa a Archivados. Las oportunidades lo conservan; restáurelo desde la pestaña Archivados.',
    ],

    'tabs' => [
        'active'   => 'Activos',
        'archived' => 'Archivados',
    ],

    'notifications' => [
        'created'  => 'Plan recurrente creado',
        'updated'  => 'Plan recurrente actualizado',
        'archived' => 'Plan recurrente archivado',
        'restored' => 'Plan recurrente restaurado',
        'deleted'  => 'Plan recurrente eliminado',
    ],

    'opportunity' => [
        'recurring-revenue' => 'Ingreso recurrente',
        'plan'              => 'Plan recurrente',
        'mrr'               => 'Ingreso recurrente mensual',
    ],
];
