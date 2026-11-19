<?php

return [
    'title'    => 'Ativo da Empresa',
    'category' => [
        'circular-reference' => 'Uma categoria não pode ser seu próprio ancestral nem criar uma referência circular.',
    ],
    'deletion' => [
        'held'                  => 'Não é possível excluir um ativo que está atualmente sob custódia. Devolva, transfira, descarte ou marque como perdido primeiro.',
        'available-has-history' => 'Não é possível arquivar ou excluir um ativo disponível com histórico de custódia. Descarte-o ou marque-o como perdido primeiro.',
        'has-history'           => 'Não é possível excluir permanentemente um ativo que possui histórico de custódia.',
        'category-referenced'   => 'Não é possível excluir uma categoria que possui subcategorias ou produtos de ativos vinculados.',
        'product-in-use'        => 'Não é possível excluir ou desativar o produto :product porque ele está vinculado a unidades de ativos.',
    ],
    'company'  => [
        'immutable' => 'Não é possível alterar a empresa de um ativo em custódia ou com histórico.',
    ],
    'product-type-immutable' => 'O tipo de produto não pode ser alterado porque já existem registros de ativos ou lotes para este produto.',
];
