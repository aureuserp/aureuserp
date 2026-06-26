<?php

return [
    'title' => 'Código de barras',

    'navigation' => [
        'back'        => 'Voltar',
        'home'        => 'Operações',
        'search'      => 'Buscar...',
        'label'       => 'Navegação',
        'open'        => 'Abrir navegação',
        'coming-soon' => 'Em breve',
    ],

    'auth' => [
        'login-title'       => 'Login do código de barras',
        'login-heading'     => 'Entrar no código de barras',
        'login-subheading'  => 'Continue para o app de operações por código de barras.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Código de barras',
            'label' => 'App de código de barras',
        ],
    ],

    'dashboard' => [
        'operations' => 'Operações',
        'empty'      => 'Nenhuma operação disponível.',
    ],

    'operation-search' => [
        'placeholder'    => 'Escaneie ou informe o código de barras da operação...',
        'open'           => 'Abrir',
        'not-found'      => 'Nenhuma operação ativa encontrada para este código de barras.',
        'multiple-found' => ':count operações correspondentes encontradas.',
    ],

    'transfers' => [
        'title' => 'Transferências',
        'empty' => 'Nenhuma transferência encontrada.',
    ],

    'adjustments' => [
        'title'             => 'Ajustes de estoque',
        'subtitle'          => 'Conte o estoque por local, produto ou lote',
        'search'            => 'Escaneie ou busque por local, produto, lote, série...',
        'empty'             => 'Nenhuma quantidade de estoque encontrada.',
        'location-scanned'  => 'Escaneando o local :location. Escaneie mais produtos aqui ou escaneie outro local.',
        'location-cleared'  => 'Filtros de ajuste de estoque limpos.',
        'product-not-found' => 'Este produto não está disponível na seleção de estoque atual.',
        'lot-not-found'     => 'Este lote ou série não está disponível na seleção de estoque atual.',
        'multiple-found'    => ':count quantidades de estoque correspondentes encontradas.',
        'count-saved'       => 'Contagem de estoque salva.',
        'count-applied'     => 'Ajuste de estoque aplicado.',
        'count-cleared'     => 'Contagem de estoque limpa.',
        'counted'           => 'Contado',
        'on-hand'           => 'Em estoque',
        'location'          => 'Local',
        'product'           => 'Produto',
        'lot-serial'        => 'Lote/Série',
        'clear-filters'     => 'Limpar filtros',
        'apply'             => 'Aplicar',
        'clear'             => 'Limpar',
        'editor-title'      => 'Detalhes do ajuste',
        'editor-subtitle'   => 'Revise os detalhes do estoque e atualize a quantidade contada.',
        'editor-image'      => 'Imagem da quantidade em estoque',
        'edit-tooltip'      => 'Editar quantidade em estoque',
    ],

    'operation' => [
        'scan'                 => 'Escaneie um produto, lote, pacote, embalagem ou transferência',
        'manual-scan'          => 'Escaneie ou busque por produto, referência, código de barras...',
        'search'               => 'Buscar produto, referência, código de barras...',
        'moves'                => 'Movimentações',
        'source'               => 'De',
        'available'            => 'Disponível',
        'discard'              => 'Descartar',
        'confirm'              => 'Confirmar',
        'counted'              => 'Contado',
        'lot-serial'           => 'Número de lote/série',
        'stock-title'          => 'Quantidade em estoque',
        'empty-moves'          => 'Nenhuma movimentação encontrada.',
        'details-title'        => 'Detalhes da movimentação',
        'settings-title'       => 'Configurações da movimentação',
        'pick-from'            => 'Separar de',
        'destination-location' => 'Local de destino',
        'destination-package'  => 'Pacote de destino',
        'select-package'       => 'Selecionar pacote',
        'stock-subtitle'       => 'Selecione de onde mais separar o produto',
        'no-stock-locations'   => 'Nenhum local de estoque encontrado.',
        'camera-unavailable'   => 'Câmera indisponível',
        'submit-scan'          => 'Enviar leitura',
        'image-alt'            => 'Imagem da linha de movimentação',
        'edit-tooltip'         => 'Editar linha de movimentação',
    ],

    'scan' => [
        'empty'                    => 'Informe ou escaneie um código de barras.',
        'not-found'                => 'Nenhum código de barras correspondente encontrado.',
        'operation-matched'        => 'Transferência encontrada.',
        'product-not-on-operation' => 'Este produto não faz parte da operação.',
        'package-matched'          => 'Pacote encontrado.',
        'move-located'             => 'Movimentação localizada. Informe a quantidade contada.',
        'move-updated'             => 'Quantidade da movimentação atualizada.',
        'move-counted'             => 'Movimentação marcada como contada.',
    ],

    'actions' => [
        'confirm'                  => 'Confirmar',
        'confirm-prompt'           => 'Tem certeza de que deseja',
        'cancel'                   => 'Cancelar',
        'check-availability'       => 'Verificar disponibilidade',
        'validate'                 => 'Validar',
        'return'                   => 'Devolver',
        'stay-on-transfer'         => 'Descartar',
        'no-backorder'             => 'Sem pedido pendente',
        'backorder-title'          => 'Transferência incompleta',
        'backorder-prompt'         => 'Se você validar agora, os produtos restantes serão adicionados a um pedido pendente.',
        'backorder-col-product'    => 'Produto',
        'backorder-col-done-todo'  => 'Concluído / A fazer',
        'backorder-col-backorder'  => 'Pedido pendente',
        'completed'                => 'Ação concluída.',
        'unsupported'              => 'Ação de código de barras não suportada.',
        'no-moves'                 => 'Esta operação não possui movimentações.',
        'no-return-quantities'     => 'Não há quantidades para devolver.',
    ],
];
