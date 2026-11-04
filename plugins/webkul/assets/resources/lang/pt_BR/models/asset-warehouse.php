<?php

return [
    'title'        => 'Depósito de ativos',
    'deletion'     => [
        'referenced' => 'Não é possível excluir um depósito referenciado por registros de custódia ou ativos.',
    ],
    'deactivation' => [
        'has-assets' => 'Não é possível desativar um depósito enquanto ele contiver ativos armazenados.',
    ],
    'company'      => [
        'immutable' => 'Não é possível alterar a empresa de um depósito referenciado por registros de custódia ou ativos.',
    ],
];
