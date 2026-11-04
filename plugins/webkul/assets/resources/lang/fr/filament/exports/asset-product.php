<?php

return [
    'columns' => [
        'name'                    => 'Nom du Produit',
        'type'                    => 'Type de Produit',
        'category'                => 'Catégorie',
        'model'                   => 'Modèle',
        'cost'                    => 'Coût',
        'assets-count'            => 'Actifs Enregistrés',
        'available-assets-count'  => 'Disponible en entrepôt',
        'held-assets-count'       => 'Attribué en garde',
        'terminated-assets-count' => 'Mis au rebut / Perdu',
        'total-cost'              => 'Évaluation totale',
        'is-active'               => 'Actif',
        'company'                 => 'Société',
        'created-at'              => 'Créé le',
        'updated-at'              => 'Mis à jour le',
    ],

    'notification' => [
        'completed' => 'Votre export de produits est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
