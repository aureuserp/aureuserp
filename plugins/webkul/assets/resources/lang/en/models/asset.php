<?php

return [
    'title'    => 'Company Asset',
    'category' => [
        'circular-reference' => 'A category cannot be its own ancestor or create a circular reference.',
    ],
    'deletion' => [
        'held'                  => 'Cannot delete an asset that is currently held. Return, transfer, scrap, or mark it lost first.',
        'available-has-history' => 'Cannot archive or delete an available asset that has prior custody history. Scrap or mark it as lost first to record the movement and reason.',
        'has-history'           => 'Cannot permanently delete an asset that has custody history.',
        'category-referenced'   => 'Cannot delete a category that has subcategories or associated asset products.',
        'product-in-use'        => 'Cannot delete or deactivate product :product because it is referenced by asset units.',
    ],
    'company'  => [
        'immutable' => 'Cannot change company for an asset that is held or has custody history.',
    ],
    'product-type-immutable' => 'The product type cannot be changed because asset or batch records already exist for this product.',
];
