<?php

return [
    'title' => 'Gestionar contactos',

    'form' => [
        'sections' => [
            'contact-form' => [
                'title'       => 'Contáctenos',
                'description' => 'El formulario Contáctenos del sitio web y qué ocurre cuando un visitante lo envía.',

                'fields' => [
                    'enabled'        => 'Activar Contáctenos',
                    'enabled-helper' => 'Muestra la página Contáctenos y su enlace en el menú del sitio web.',
                    'action'         => 'Después del envío',
                    'email-helper'   => 'El mensaje se envía por correo a :email.',
                    'crm-helper'     => 'El mensaje crea un registro en el CRM con los datos del visitante.',
                ],
            ],

            'contacts' => [
                'title' => 'Contactos',

                'fields' => [
                    'email' => 'Correo electrónico',
                    'phone' => 'Teléfono',
                ],
            ],

            'social-links' => [
                'title' => 'Redes sociales',

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
