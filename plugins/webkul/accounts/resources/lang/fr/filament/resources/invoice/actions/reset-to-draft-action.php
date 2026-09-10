<?php

return [
    'title' => 'Remettre en brouillon',

    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'État de l\'écriture comptable invalide',
                    'body'  => 'Seules les écritures comptables comptabilisées ou annulées peuvent être remises en brouillon.',
                ],
            ],
        ],
    ],
];
