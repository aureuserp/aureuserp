<?php

return [
    'title'                   => 'Profil',
    'heading'                 => 'Profil',
    'subheading'              => 'Gérez les paramètres et préférences de votre compte.',
    'information_section'     => 'Informations du profil',
    'information_description' => "Mettez à jour les informations du profil de votre compte et votre adresse e-mail.",

    'notification' => [
        'success' => [
            'title' => 'Profil mis à jour',
            'body'  => 'Votre profil a été mis à jour avec succès.',
        ],

        'error' => [
            'title' => 'Échec de la mise à jour du profil',
            'body'  => "Une erreur s'est produite lors de la mise à jour de votre profil.",
        ],

        'validation-error' => [
            'title' => 'Erreur de validation',
        ],
    ],

    'actions' => [
        'save' => 'Enregistrer les modifications',
    ],

    'fields' => [
        'avatar'          => 'Photo de profil',
        'name'            => 'Nom',
        'email'           => 'E-mail',
        'language'        => 'Langue préférée',
        'language_helper' => "L'interface d'administration sera affichée dans cette langue.",
    ],

    'password' => [
        'section'     => 'Mettre à jour le mot de passe',
        'description' => 'Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.',
        'current'     => 'Mot de passe actuel',
        'new'         => 'Nouveau mot de passe',
        'confirm'     => 'Confirmer le mot de passe',
        'helper'      => 'Doit comporter au moins 8 caractères.',

        'errors' => [
            'current-required'  => 'Le mot de passe actuel est requis.',
            'current-incorrect' => 'Le mot de passe actuel est incorrect. Veuillez réessayer.',
            'same-as-current'   => 'Le nouveau mot de passe doit être différent de votre mot de passe actuel.',
        ],

        'current-helper' => 'Saisissez votre mot de passe actuel pour vérifier votre identité.',

        'notification' => [
            'success' => [
                'title' => 'Mot de passe mis à jour',
                'body'  => 'Votre mot de passe a été mis à jour avec succès.',
            ],

            'error' => [
                'title' => 'Échec de la mise à jour du mot de passe',
                'body'  => "Une erreur s'est produite lors de la mise à jour de votre mot de passe.",
            ],
        ],
    ],
];
