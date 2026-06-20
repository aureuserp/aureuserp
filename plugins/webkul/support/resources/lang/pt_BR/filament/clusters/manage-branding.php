<?php

return [
    'breadcrumb' => 'Marca',
    'title' => 'Marca',
    'group' => 'Geral',

    'navigation' => [
        'label' => 'Marca',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title' => 'Logo e favicon',
                'description' => 'Substitua os logos, o favicon e a altura do logo usados nos painéis administrativo e do cliente. Deixe um campo vazio para manter o padrão.',
            ],
            'colors' => [
                'title' => 'Cores',
                'description' => 'Substitua as cores do tema usadas nos painéis administrativo e do cliente. Deixe uma cor vazia para manter o padrão.',
            ],
        ],
        'fields' => [
            'light-logo' => 'Logo claro',
            'light-logo-helper' => 'Exibido em fundos claros. Substitui o logo padrão.',
            'dark-logo' => 'Logo escuro',
            'dark-logo-helper' => 'Exibido quando o modo escuro está ativado.',
            'favicon' => 'Favicon',
            'favicon-helper' => 'Ícone da aba do navegador.',
            'logo-height' => 'Altura do logo',
            'logo-height-helper' => 'Um valor de altura CSS, por exemplo, 2rem ou 40px.',
            'primary-color' => 'Primário',
            'gray-color' => 'Cinza',
            'danger-color' => 'Perigo',
            'info-color' => 'Informação',
            'success-color' => 'Sucesso',
            'warning-color' => 'Aviso',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Redefinir para o padrão',
        ],
    ],
];
