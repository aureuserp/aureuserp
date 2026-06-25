<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Informe um Cliente válido para prosseguir com a validação da Fatura de cliente.',
        'vendor-required'      => 'Informe um Fornecedor válido para prosseguir com a validação da Fatura de fornecedor.',
        'bank-archived'        => 'A Conta bancária do parceiro selecionada vinculada a esta fatura está arquivada.',
        'negative-amount'      => 'A fatura não pode ser confirmada com valor total negativo.',
        'date-required'        => 'Informe uma Data da fatura/reembolso válida para prosseguir com a validação da fatura/reembolso.',
        'currency-archived'    => 'Você não pode confirmar uma fatura com uma moeda arquivada.',
        'account-deprecated'   => 'Uma ou mais linhas desta fatura usam contas obsoletas.',
        'lines-required'       => 'Adicione pelo menos uma linha à fatura.',
        'draft-state-required' => 'Somente faturas em rascunho podem ser confirmadas.',
        'journal-archived'     => 'Você não pode confirmar uma fatura com um diário arquivado.',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'Fatura nº :name',
            'bill'        => 'Fatura de fornecedor nº :name',
            'refund'      => 'Reembolso nº :name',
            'credit-note' => 'Nota de crédito nº :name',
        ],

        'labels' => [
            'invoice-date'          => 'Data da fatura',
            'bill-date'             => 'Data',
            'refund-date'           => 'Data do reembolso',
            'credit-note-date'      => 'Data da nota de crédito',
            'source'                => 'Origem',
            'due-date'              => 'Data de vencimento',
            'product'               => 'Produto',
            'quantity'              => 'Quantidade',
            'unit'                  => 'Unidade',
            'unit-price'            => 'Preço unitário',
            'subtotal'              => 'Subtotal',
            'tax'                   => 'Imposto',
            'discount'              => 'Desconto',
            'grand-total'           => 'Total geral',
            'payment-information'   => 'Informações de pagamento',
            'payment-communication' => 'Comunicação de pagamento',
            'account-details'       => 'nestes dados bancários:',
        ],
    ],
];
