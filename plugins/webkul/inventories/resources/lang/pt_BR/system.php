<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Nada para verificar disponibilidade.',
        ],

        'cancel-move' => [
            'already-done' => 'Você não pode cancelar uma movimentação de estoque definida como \'Concluída\'. Crie uma devolução para reverter as movimentações realizadas.',
        ],

        'unreserve-move' => [
            'already-done' => "You can not unreserve a stock move that has been set to 'Done'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'A quantidade concluída para o produto ":product" não respeita a precisão de arredondamento definida na unidade de medida ":unit". Altere a quantidade concluída ou a precisão de arredondamento da sua unidade de medida.',
            'no-negative-quantities'     => 'Quantidades negativas não são permitidas',
            'missing-lot-serial-number'  => "You need to supply a Lot/Serial Number for product:\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "No rule has been found to replenish \":product\" in \":location\".\nVerify the routes configuration on the product.",
            'no-source-location' => 'Nenhum local de origem definido na regra de estoque: :name!',
            'no-vendor-price'    => 'Não há preço de fornecedor correspondente para gerar o pedido de compra do produto :product (nenhum fornecedor definido, quantidade mínima não atingida, datas inválidas, ...). Acesse o formulário do produto e complete a lista de fornecedores.',
        ],

        'return' => [
            'origin' => 'Devolução de :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Não é permitido reservar uma quantidade negativa.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Quantidade ou quantidade reservada deve ser definida.',
        'removal-strategy-not-implemented' => 'Estratégia de remoção :strategy não implementada.',
        'unreserve-more-than-stock'        => 'Não é possível cancelar a reserva de mais produtos de :name do que você tem em estoque.',
    ],

    'product' => [
        'endless-loop-rule' => "Invalid rule's configuration, the following rule causes an endless loop: :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'A quantidade concluída para o produto :product não respeita a precisão de arredondamento definida na unidade de medida :unit. Altere a quantidade concluída ou a precisão de arredondamento da sua unidade de medida.',
        'split-done-or-cancel'       => 'Você não pode dividir uma movimentação de estoque definida como \'Concluída\' ou \'Cancelada\'.',
        'split-draft'                => 'Você não pode dividir uma movimentação em rascunho. Ela precisa ser confirmada primeiro.',
        'serial-already-assigned'    => 'O número de série já foi atribuído ao produto: :product, número de série: :serial_number',
    ],

    'rule' => [
        'delay-on'     => 'Atraso em :name',
        'days'         => '+ :days dia(s)',
        'time-horizon' => 'Horizonte de tempo',
    ],
];
