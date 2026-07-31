<?php

return [
    'title' => 'Perfil',
    'heading' => 'Perfil',
    'subheading' => 'Gerencie as configurações e preferências da sua conta.',
    'information_section' => 'Informações do perfil',
    'information_description' => 'Atualize as informações de perfil e o endereço de e-mail da sua conta.',

    'notification' => [
        'success' => [
            'title' => 'Perfil atualizado',
            'body' => 'Seu perfil foi atualizado com sucesso.',
        ],

        'error' => [
            'title' => 'Falha ao atualizar perfil',
            'body' => 'Ocorreu um erro ao atualizar seu perfil.',
        ],

        'validation-error' => [
            'title' => 'Erro de validação',
        ],
    ],

    'actions' => [
        'save' => 'Salvar alterações',
    ],

    'fields' => [
        'avatar' => 'Foto do perfil',
        'name' => 'Nome',
        'email' => 'E-mail',
        'language' => 'Idioma preferido',
        'language_helper' => 'A interface administrativa será exibida neste idioma.',
    ],

    'password' => [
        'section' => 'Atualizar senha',
        'description' => 'Garanta que sua conta use uma senha longa e aleatória para permanecer segura.',
        'current' => 'Senha atual',
        'new' => 'Nova senha',
        'confirm' => 'Confirmar senha',
        'helper' => 'Deve ter pelo menos 8 caracteres.',

        'errors' => [
            'current-required' => 'A senha atual é obrigatória.',
            'current-incorrect' => 'A senha atual está incorreta. Tente novamente.',
            'same-as-current' => 'A nova senha deve ser diferente da sua senha atual.',
        ],

        'current-helper' => 'Digite sua senha atual para verificar sua identidade.',

        'notification' => [
            'success' => [
                'title' => 'Senha atualizada',
                'body' => 'Sua senha foi atualizada com sucesso.',
            ],

            'error' => [
                'title' => 'Falha ao atualizar senha',
                'body' => 'Ocorreu um erro ao atualizar sua senha.',
            ],
        ],
    ],
];
