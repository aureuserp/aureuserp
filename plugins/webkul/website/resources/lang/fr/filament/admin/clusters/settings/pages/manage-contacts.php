<?php

return [
    'title' => 'Gérer les contacts',

    'form' => [
        'sections' => [
            'contact-form' => [
                'title'       => 'Contactez-nous',
                'description' => 'Le formulaire Contactez-nous du site web et ce qui se passe quand un visiteur l\'envoie.',

                'fields' => [
                    'enabled'        => 'Activer Contactez-nous',
                    'enabled-helper' => 'Affiche la page Contactez-nous et son lien dans le menu du site web.',
                    'action'         => 'Après envoi',
                    'email-helper'   => 'Le message est envoyé par e-mail à :email.',
                    'crm-helper'     => 'Le message crée un enregistrement dans le CRM avec les coordonnées du visiteur.',
                ],

                'actions' => [
                    'email'       => 'Envoyer un e-mail',
                    'lead'        => 'Créer une piste',
                    'opportunity' => 'Créer une opportunité',
                ],
            ],

            'contacts' => [
                'title' => 'Contacts',

                'fields' => [
                    'email' => 'Email',
                    'phone' => 'Téléphone',
                ],
            ],

            'social-links' => [
                'title' => 'Liens sociaux',

                'fields' => [
                    'twitter'   => 'Twitter',
                    'facebook'  => 'Facebook',
                    'instagram' => 'Instagram',
                    'linkedin'  => 'LinkedIn',
                    'pinterest' => 'Pinterest',
                    'tiktok'    => 'TikTok',
                    'github'    => 'GitHub',
                    'slack'     => 'Slack',
                    'whatsapp'  => 'WhatsApp',
                    'youtube'   => 'YouTube',
                ],
            ],
        ],
    ],
];
