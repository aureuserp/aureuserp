<?php

return [
    'move' => [
        'no-journal-found-title' => 'A contabilidade não está configurada',
        'no-journal-found'       => 'Nenhum diário foi encontrado na empresa :company para nenhum destes tipos: :types. Configure primeiro um plano de contas para esta empresa.',
    ],

    'posting' => [
        'invalid-entry'         => 'O lançamento contábil é inválido: :field.',
        'unbalanced'            => 'O lançamento contábil :move não está balanceado: débito :debit, crédito :credit.',
        'rejected-by-validator' => 'O lançamento foi rejeitado: :reason',
        'move-not-postable'     => 'O lançamento contábil não pode ser lançado: :reason',
        'move-not-reversible'   => 'O lançamento contábil :move não pode ser estornado.',
        'duplicate-source'      => 'A chave de origem já foi usada pelo lançamento contábil :move com linhas diferentes.',
    ],

    'tax-formula' => [
        'empty'                => 'A fórmula não pode estar vazia.',
        'invalid-character'    => 'A fórmula contém um caractere inválido: :character',
        'unexpected-token'     => 'A fórmula contém um elemento inesperado: :token',
        'unexpected-end'       => 'A fórmula termina de forma inesperada.',
        'unclosed-parenthesis' => 'A fórmula contém um parêntese não fechado.',
        'unknown-variable'     => 'Variável desconhecida ":variable". Apenas estas variáveis estão disponíveis: :variables',
        'unknown-function'     => 'Função desconhecida ":function". Apenas estas funções estão disponíveis: :functions',
    ],
];
