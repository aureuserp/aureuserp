<?php

return [
    'title' => 'Redefinir para rascunho',

    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'Estado do lançamento contábil inválido',
                    'body' => 'Somente lançamentos contábeis publicados ou cancelados podem ser redefinidos para rascunho.',
                ],
            ],
        ],
    ],
];
