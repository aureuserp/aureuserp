<?php

return [
    'title' => 'Local de Ativos e Custódias',

    'deletion' => [
        'referenced' => 'Não é possível excluir um local referenciado por registros de custódia ou ativos.',
    ],

    'deactivation' => [
        'open-custody' => 'Não é possível desativar um local enquanto ele mantiver registros de custódia abertos.',
    ],

    'company' => [
        'immutable' => 'Não é possível alterar a empresa de um local referenciado por registros de custódia ou ativos.',
    ],

    'hierarchy' => [
        'circular-reference' => 'Um local não pode ser definido como filho de si mesmo ou de um de seus descendentes.',
    ],

    'usage' => [
        'has-fixed-assets'   => 'Não é possível alterar o tipo de uso do local para zona de custódia enquanto ele ou seus sublocais tiverem ativos fixos implantados sem um funcionário.',
        'has-custody-assets' => 'Não é possível alterar o tipo de uso do local para localização de ativos enquanto ele ou seus sublocais tiverem ativos de custódia de funcionários ativos.',
    ],
];
