<?php

return [
    'title' => 'Manage Contacts',

    'form' => [
        'sections' => [
            'contact-form' => [
                'title'       => 'Contact Us',
                'description' => 'The Contact Us form of the website and what happens when a visitor sends it.',

                'fields' => [
                    'enabled'        => 'Enable Contact Us',
                    'enabled-helper' => 'Shows the Contact Us page and its link in the website menu.',
                    'action'         => 'After submission',
                    'email-helper'   => 'The message is emailed to :email.',
                    'crm-helper'     => 'The message creates a record in CRM with the visitor\'s details.',
                ],

                'actions' => [
                    'email'       => 'Send an email',
                    'lead'        => 'Create a lead',
                    'opportunity' => 'Create an opportunity',
                ],
            ],

            'contacts' => [
                'title' => 'Contacts',

                'fields' => [
                    'email' => 'Email',
                    'phone' => 'Phone',
                ],
            ],

            'social-links' => [
                'title' => 'Social Links',

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
