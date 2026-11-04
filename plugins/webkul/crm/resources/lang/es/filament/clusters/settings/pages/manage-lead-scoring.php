<?php

return [
    'title'       => 'Gestionar puntuación de leads',
    'description' => 'La probabilidad de ganar cada lead y oportunidad abiertos se calcula a partir de su historial de ganados y perdidos. La etapa y el equipo de ventas siempre se usan.',

    'form' => [
        'variables'          => 'Considerar también',
        'variables-helper'   => 'Qué datos de un registro tiene en cuenta la probabilidad.',
        'start-date'         => 'Considerar leads creados a partir del',
        'start-date-helper'  => 'Déjelo vacío para usar todo el historial.',
        'update'             => 'Actualizar probabilidades de puntuación',
        'update-description' => 'Guarda estas opciones, vuelve a aprender del historial de ganados y perdidos y recalcula todos los registros abiertos.',
        'updated'            => 'Probabilidades actualizadas para :count registro(s) abierto(s).',
    ],
];
