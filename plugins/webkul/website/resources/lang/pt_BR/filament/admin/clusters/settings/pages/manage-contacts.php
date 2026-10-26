<?php

return [
    'title' => 'Gerenciar contatos',

    'form' => [
        'sections' => [
            'contact-form' => [
                'title'       => 'Fale conosco',
                'description' => 'O formulário Fale conosco do site e o que acontece quando um visitante o envia.',

                'fields' => [
                    'enabled'        => 'Ativar Fale conosco',
                    'enabled-helper' => 'Mostra a página Fale conosco e o link no menu do site.',
                    'action'         => 'Após o envio',
                    'email-helper'   => 'A mensagem é enviada por e-mail para :email.',
                    'crm-helper'     => 'A mensagem cria um registro no CRM com os dados do visitante.',
                ],

                'actions' => [
                    'email'       => 'Enviar um e-mail',
                    'lead'        => 'Criar um lead',
                    'opportunity' => 'Criar uma oportunidade',
                ],
            ],

            'contacts' => [
                'title' => 'Contatos',

                'fields' => [
                    'email' => 'E-mail',
                    'phone' => 'Telefone',
                ],
            ],

            'social-links' => [
                'title' => 'Links sociais',

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
