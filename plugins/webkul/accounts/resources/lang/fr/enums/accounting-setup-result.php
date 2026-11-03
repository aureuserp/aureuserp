<?php

return [
    'completed'      => 'Comptabilité configurée',
    'already-set-up' => 'La comptabilité est déjà configurée',
    'no-template'    => 'Aucune société modèle utilisable',
    'incomplete'     => 'La configuration comptable n\'a pas abouti',

    'descriptions' => [
        'completed'      => 'Le plan comptable, les journaux et les paramètres par défaut ont été créés pour cette société.',
        'already-set-up' => 'Cette société possède déjà ses journaux de ventes et d\'achats. Rien n\'a été modifié.',
        'no-template'    => 'Cette société est elle-même la société par défaut utilisée comme modèle, ou aucune société par défaut n\'existe : il n\'y a donc rien à partir de quoi copier le plan comptable, les journaux et les paramètres par défaut. Rien n\'a été créé. Définissez une autre société entièrement configurée comme société par défaut, puis réessayez.',
        'incomplete'     => 'La société par défaut utilisée comme modèle n\'a pas de journal de ventes ou d\'achats, ou l\'un de ses paramètres par défaut de compte, de journal ou de taxe est vide ou désigne un enregistrement qui ne lui appartient pas. Rien n\'a été créé pour cette société. Complétez la comptabilité de la société par défaut, puis réessayez. Les détails techniques ont été enregistrés.',
    ],
];
