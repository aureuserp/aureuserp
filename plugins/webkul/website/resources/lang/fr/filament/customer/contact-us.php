<?php

return [
    'title'       => 'Contactez-nous',
    'intro'       => 'Contactez-nous pour toute question concernant notre entreprise ou nos services.',
    'intro-reply' => 'Nous ferons de notre mieux pour vous répondre au plus vite.',
    'submit'      => 'Envoyer',
    'lead-note'   => 'Créé depuis le formulaire Contactez-nous du site web.',

    'fields' => [
        'name'     => 'Nom',
        'phone'    => 'Numéro de téléphone',
        'email'    => 'E-mail',
        'company'  => 'Société',
        'subject'  => 'Objet',
        'question' => 'Question',
    ],

    'validation' => [
        'no-markup' => 'Veuillez saisir du texte brut uniquement. Le HTML et les scripts ne sont pas autorisés.',
        'phone'     => 'Veuillez saisir un numéro de téléphone valide (chiffres, espaces, +, -, parenthèses).',
    ],

    'notifications' => [
        'failed'   => 'Votre message n\'a pas pu être envoyé. Réessayez plus tard.',
        'too-many' => 'Trop de messages. Réessayez dans :minutes minute(s).',

        'sent' => [
            'title' => 'Merci !',
            'body'  => 'Votre message a été envoyé. Nous vous répondrons bientôt.',
        ],
    ],

    'mail' => [
        'subject' => 'Contact du site web : :subject',
        'intro'   => 'Un nouveau message a été envoyé depuis le formulaire Contactez-nous du site web.',
    ],
];
