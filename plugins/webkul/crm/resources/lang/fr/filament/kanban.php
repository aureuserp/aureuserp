<?php

return [
    'add-column' => [
        'stage'         => 'Ajouter une étape',
        'activity-type' => 'Ajouter un type d\'activité',
        'placeholder'   => 'Nom…',
        'add'           => 'Ajouter',
        'cancel'        => 'Annuler',
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
];
