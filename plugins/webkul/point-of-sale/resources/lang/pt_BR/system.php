<?php

return [
    'unexpected-error' => 'Algo deu errado no servidor. Tente novamente e chame um gerente se continuar acontecendo.',

    'products' => [
        'tip'      => 'Gorjetas',
        'discount' => 'Desconto',
    ],

    'picking' => [
        'operation-type-missing' => 'Nenhum tipo de operação de estoque está configurado para o pedido :order, então nada foi movimentado.',
    ],

    'config' => [
        'terminal-journal' => 'Ponto de venda',
    ],

    'order-workflow' => [
        'customer' => [
            'required-by-terminal'       => 'Este ponto de venda exige um cliente em todos os pedidos.',
            'required-to-invoice'        => 'Selecione um cliente antes de faturar este pedido.',
            'required-to-ship'           => 'Selecione um cliente antes de enviar este pedido depois.',
            'shipping-address-required'  => 'O cliente selecionado precisa de um endereço de entrega antes de enviar este pedido depois.',
            'required-by-payment-method' => 'O método de pagamento selecionado exige um cliente.',
        ],

        'mark-paid' => [
            'already-settled'          => 'O pedido :order já foi liquidado.',
            'insufficient-payment'     => 'O pedido :order não está totalmente pago.',
            'cash-method-required'     => 'O pedido :order não pode ter pagamento a maior porque este terminal não tem um método de pagamento em dinheiro configurado.',
        ],

        'cancel' => [
            'not-draft' => 'O pedido :order não pode mais ser cancelado.',
        ],

        'split' => [
            'not-draft' => 'O pedido :order não pode mais ser dividido.',
        ],

        'tip' => [
            'not-enabled' => 'Ative as gorjetas e defina um produto de gorjeta no ponto de venda primeiro.',
        ],

        'refund' => [
            'not-refundable'    => 'O pedido :order não pode ser reembolsado.',
            'exceeds-sold'      => 'A quantidade reembolsada excede o que foi vendido de :product.',
            'no-payment-method' => 'O terminal :order não tem método de pagamento para reembolsar.',
            'nothing-to-refund' => 'Selecione ao menos uma linha para reembolsar.',
        ],
    ],

    'global-discount' => [
        'not-enabled' => 'Ative o desconto global e defina um produto de desconto no ponto de venda primeiro.',
        'not-draft'   => 'O pedido :order não pode mais receber desconto.',
    ],

    'terminal-product-creator' => [
        'name-required'    => 'Dê um nome ao produto.',
        'defaults-missing' => 'Configure uma unidade de medida e uma categoria de produto antes de criar produtos no caixa.',
    ],

    'payment-method' => [
        'in-open-session' => 'Feche as sessões abertas :sessions antes de excluir esta forma de pagamento.',
    ],

    'payment-method-provisioner' => [
        'cash'         => 'Dinheiro',
        'cash-journal' => 'Dinheiro',
    ],

    'order-sync' => [
        'product-missing'         => 'Um produto deste pedido não existe mais. Remova-o e tente novamente.',
        'customer-missing'        => 'O cliente deste pedido não existe mais. Escolha outro cliente.',
        'table-missing'           => 'A mesa deste pedido não existe mais. Mova o pedido para outra mesa.',
        'price-list-missing'      => 'A lista de preços deste pedido não existe mais.',
        'fiscal-position-missing' => 'A posição fiscal deste pedido não existe mais.',
        'payment-method-missing'  => 'Uma forma de pagamento deste pedido não existe mais.',
        'refunded-order-missing'  => 'O pedido sendo reembolsado não foi encontrado.',
        'customer-email-invalid'  => 'O endereço de e-mail do cliente não é válido.',
        'discount-invalid'        => 'Os descontos de linha devem estar entre 0 e 100%.',
    ],

    'order-processor' => [
        'price-locked'           => 'Alterar o preço de :product exige um gerente.',
        'line-discount-disabled' => 'Os descontos por linha estão desativados neste ponto de venda.',
        'foreign-order'          => 'Este pedido pertence a outro caixa e não pode ser alterado aqui.',
        'foreign-line'           => 'Um item deste pedido pertence a outro pedido. Recarregue o caixa e tente novamente.',
    ],

    'floor-plan' => [
        'tables-in-use' => 'As mesas :tables ainda têm pedidos abertos. Pague, libere ou mova-os primeiro.',
    ],

    'session-preflight' => [
        'draft-orders' => 'Pague ou cancele estes pedidos antes de fechar a sessão: :orders.',

        'journal' => [
            'missing'      => 'Defina um diário de vendas no ponto de venda.',
            'invalid-type' => 'O diário do ponto de venda deve ser um diário de vendas.',
        ],

        'invoice-journal' => [
            'missing'      => 'Defina um diário de faturas no ponto de venda.',
            'invalid-type' => 'O diário de faturas deve ser um diário de vendas.',
        ],

        'receivable-account' => [
            'missing'          => 'Defina uma conta a receber no ponto de venda.',
            'invalid-type'     => 'A conta a receber do ponto de venda deve ser uma conta a receber.',
            'not-reconcilable' => 'A conta a receber do ponto de venda deve permitir conciliação.',
            'deprecated'       => 'A conta a receber do ponto de venda está obsoleta.',
        ],

        'payment-methods' => [
            'missing'               => 'Adicione ao menos um método de pagamento ao ponto de venda.',
            'pay-later-unsupported' => 'O método de pagamento :method não tem diário e contas de cliente ainda não são suportadas.',
            'journal-missing'       => 'Defina um diário no método de pagamento :method.',
            'journal-company'       => 'O diário de :method pertence a outra empresa.',
            'receivable-missing'    => 'Defina uma conta a receber para :method.',
        ],

        'cash-journal' => [
            'multiple'             => 'Apenas um método de pagamento em dinheiro é suportado por ponto de venda.',
            'profit-loss-missing'  => 'Defina as contas de lucros e perdas no diário de caixa :journal.',
        ],

        'taxes' => [
            'account-missing' => 'O imposto :tax tem uma linha de distribuição sem conta.',
        ],

        'global-discount' => [
            'product-missing' => 'Defina um produto de desconto no ponto de venda para usar descontos globais.',
        ],

        'cogs' => [
            'stock-output-missing' => 'Defina uma conta de saída de estoque antes de ativar o custo das mercadorias vendidas.',
        ],
    ],

    'invoice-payer' => [
        'entry-reference' => 'Pagamento de fatura para :order (:invoice) usando :method',
    ],

    'session-closer' => [
        'entry-reference'                 => 'Sessão do ponto de venda :session',
        'payment-difference-reference'    => 'Diferença em :method para :session',
        'cogs-reference'                  => 'Custo das mercadorias vendidas de :session',
        'sales-line'                      => 'Vendas do ponto de venda',
        'tax-line'                        => 'Impostos do ponto de venda',
        'receivable-line'                 => 'Conta a receber do ponto de venda',
        'invoice-receivable-line'         => 'Conta a receber de faturas do ponto de venda',
        'cash-line'                       => 'Caixa do ponto de venda',
        'cash-difference-line'            => 'Diferença de caixa no fechamento',
        'cogs-line'                       => 'Custo das mercadorias vendidas',
        'stock-output-line'               => 'Saída de estoque',
        'balancing-line'                  => 'Diferença no fechamento',
        'rounding-line'                   => 'Arredondamento de dinheiro',
        'unbalanced'                      => 'O lançamento de fechamento está desbalanceado em :delta. Escolha uma conta de balanceamento para lançá-lo.',
        'income-account-missing'          => 'Nenhuma conta de receita resolvida para :product.',
        'expense-account-missing'         => 'Nenhuma conta de despesa resolvida para :product.',
        'cash-account-missing'            => 'O diário de caixa não tem conta padrão.',
        'cash-difference-account-missing' => 'O diário de caixa não tem conta de lucros ou perdas.',
        'stock-output-missing'            => 'Defina uma conta de saída de estoque no ponto de venda.',
        'line-account-missing'            => 'Uma linha do lançamento de fechamento não tem conta.',
        'rounding-account-missing'        => 'O arredondamento de dinheiro não tem conta de lucros ou perdas.',
        'difference-exceeded'             => 'A diferença máxima permitida é :amount. Contate seu gerente para aceitar a diferença de fechamento.',
    ],

    'invoicer' => [
        'customer-required' => 'O pedido :order precisa de um cliente antes de poder ser faturado.',
        'journal-missing'   => 'Defina um diário de faturas no ponto de venda.',
        'session-closed'    => 'O pedido :order pertence a uma sessão fechada e já está contabilizado no lançamento de fechamento.',
    ],

    'session-workflow' => [
        'open' => [
            'already-open' => 'Já existe uma sessão aberta para :config.',
            'inactive'     => 'O registro :config está inativo e não pode ser aberto.',
        ],

        'assert-open' => [
            'not-open' => 'A sessão :session não está aberta.',
        ],

        'assert-state' => [
            'invalid' => 'A sessão :session não pode sair do estado :state.',
        ],

        'discard' => [
            'not-discardable' => 'A sessão :name tem atividade registrada e precisa ser fechada, não descartada.',
        ],

        'cash-movement' => [
            'invalid-amount' => 'O valor de um movimento de caixa deve ser maior que zero.',
        ],
    ],
];
