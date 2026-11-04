<?php

return [
    'title'    => 'Activo de la Empresa',
    'category' => [
        'circular-reference' => 'Una categoría no puede ser su propio ancestro ni crear una referencia circular.',
    ],
    'deletion' => [
        'held'                  => 'No se puede eliminar un activo que actualmente está en custodia. Devuélvalo, transfiéralo, deséchelo o márquelo como perdido primero.',
        'available-has-history' => 'No se puede archivar ni eliminar un activo disponible con historial de custodia previo. Deséchelo o márquelo como perdido primero.',
        'has-history'           => 'No se puede eliminar permanentemente un activo que tiene historial de custodia.',
        'category-referenced'   => 'No se puede eliminar una categoría que contiene subcategorías o productos de activos asociados.',
        'product-in-use'        => 'No se puede eliminar o desactivar el producto :product porque está referenciado por unidades de activos.',
    ],
    'company'  => [
        'immutable' => 'No se puede cambiar la empresa de un activo en custodia o con historial.',
    ],
    'product-type-immutable' => 'No se puede cambiar el tipo de producto porque ya existen registros de activos o lotes para este producto.',
];
