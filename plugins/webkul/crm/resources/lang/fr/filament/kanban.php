<?php

return [
    'add-column' => [
        'stage'         => 'Ajouter une étape',
        'activity-type' => 'Ajouter un type d\'activité',
        'add'           => 'Ajouter',
        'created'       => '« :name » ajouté',
        'exists'        => '« :name » existe déjà',
    ],

    'column-actions' => [
        'settings'           => 'Paramètres : :name',
        'edit'               => 'Modifier',
        'delete'             => 'Supprimer',
        'name'               => 'Nom',
        'edit-heading'       => 'Modifier « :name »',
        'delete-heading'     => 'Supprimer « :name » ?',
        'delete-description' => 'Cette action est irréversible.',
        'saved'              => '« :name » enregistré',
        'deleted'            => '« :name » supprimé',

        'in-use' => [
            'title' => 'Impossible de supprimer « :name »',
            'body'  => 'Il contient encore :count enregistrement. Déplacez-le d\'abord vers une autre colonne.|Il contient encore :count enregistrements. Déplacez-les d\'abord vers une autre colonne.',
        ],
    ],

    'empty' => [
        'heading'           => 'Rien ici pour l\'instant',
        'description'       => 'Glissez une carte ici ou créez-en une nouvelle.',
        'board-heading'     => 'Aucun enregistrement à afficher',
        'board-description' => 'Rien ne correspond à cette vue ou aux filtres actuels.',
    ],

    'progress' => [
        'label' => 'Activités de cette étape',
        'total' => 'Revenu attendu',
    ],
];
