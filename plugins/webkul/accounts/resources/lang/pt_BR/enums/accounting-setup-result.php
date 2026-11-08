<?php

return [
    'completed'      => 'Contabilidade configurada',
    'already-set-up' => 'A contabilidade já está configurada',
    'no-template'    => 'Nenhuma empresa modelo utilizável',
    'incomplete'     => 'A configuração contábil não foi concluída',

    'descriptions' => [
        'completed'      => 'O plano de contas, os diários e as configurações padrão foram criados para esta empresa.',
        'already-set-up' => 'Esta empresa já possui seus diários de vendas e compras. Nada foi alterado.',
        'no-template'    => 'Esta empresa é a própria empresa padrão usada como modelo, ou não existe empresa padrão, portanto não há de onde copiar o plano de contas, os diários e as configurações padrão. Nada foi criado. Defina outra empresa totalmente configurada como empresa padrão e tente novamente.',
        'incomplete'     => 'A empresa padrão usada como modelo não possui um diário de vendas ou de compras, ou uma de suas configurações padrão de conta, diário ou imposto está vazia ou aponta para um registro que não pertence a ela. Nada foi criado para esta empresa. Complete a contabilidade da empresa padrão e tente novamente. Os detalhes técnicos foram registrados.',
    ],
];
