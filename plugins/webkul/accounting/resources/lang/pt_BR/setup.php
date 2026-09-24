<?php

return [
    'action'       => 'Configurar a contabilidade para esta empresa',
    'notice'       => 'A contabilidade ainda não está configurada para esta empresa. Use \'Configurar contabilidade\' abaixo para criar seu plano de contas, diários e configurações padrão.',
    'notification' => [
        'title' => 'Contabilidade configurada',
        'body'  => 'O plano de contas, os diários e as configurações padrão foram criados para esta empresa.',
    ],
    'error' => [
        'title' => 'A configuração contábil não foi executada',
        'body'  => 'Nenhum plano de contas, diário ou configuração padrão foi criado. Verifique se a empresa padrão usada como modelo está totalmente configurada e tente novamente. Os detalhes técnicos foram registrados.',
    ],
];
