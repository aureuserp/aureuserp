<?php

return [
    'breadcrumb' => 'Marca',
    'title'      => 'Marca',
    'group'      => 'Geral',

    'navigation' => [
        'label' => 'Marca',
    ],

    'form' => [
        'tabs' => [
            'colors' => 'Cores da marca',
            'logo'   => 'Logo e favicon',
        ],

        'sections' => [
            'colors' => [
                'title'       => 'Cores da marca',
                'description' => 'Defina as cores usadas nos painéis administrativo e do cliente. Clique no ícone de informação de qualquer cor para pré-visualizar como ela fica antes de salvar.',
            ],
            'palettes' => [
                'title'       => 'Paletas de cores sugeridas',
                'description' => 'Escolha entre nossas paletas de cores pré-projetadas ou crie a sua própria.',
            ],
            'logo' => [
                'title'       => 'Logo e favicon',
                'description' => 'Substitua os logos e o favicon usados nos painéis administrativo e do cliente. Deixe um campo vazio para manter o padrão.',
            ],
        ],

        'fields' => [
            'light-logo'         => 'Logo claro',
            'light-logo-helper'  => 'Exibido em fundos claros. Substitui o logo padrão.',
            'dark-logo'          => 'Logo escuro',
            'dark-logo-helper'   => 'Exibido quando o modo escuro está ativado.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Ícone da aba do navegador.',
            'logo-height'        => 'Altura do logo',
            'logo-height-helper' => 'Um valor de altura CSS, por exemplo, 2rem ou 40px.',
            'primary-color'      => 'Primário',
            'gray-color'         => 'Cinza',
            'danger-color'       => 'Perigo',
            'info-color'         => 'Informação',
            'success-color'      => 'Sucesso',
            'warning-color'      => 'Aviso',
        ],

        'palettes' => [
            'coral'       => 'Vermelho coral',
            'ocean'       => 'Azul oceano',
            'forest'      => 'Verde floresta',
            'purple'      => 'Sonho roxo',
            'modern'      => 'Cinza moderno',
            'apply'       => 'Usar esta',
            'custom-hint' => 'Precisa de algo diferente? Defina suas próprias cores com os seletores acima.',
        ],

        'preview' => [
            'title'             => 'Pré-visualização ao vivo',
            'heading'           => 'Pré-visualização de cores',
            'description'       => 'Veja como suas cores aparecerão em toda a interface antes de salvar.',
            'hint'              => 'Clique no ícone de informação de qualquer cor para ver uma pré-visualização ao vivo de como as cores selecionadas aparecem em todo o aplicativo.',
            'shade-note'        => 'Tom 600 (usado para botões)',
            'light'             => 'Tema claro',
            'dark'              => 'Tema escuro',
            'search'            => 'Pesquisar...',
            'primary-button'    => 'Botão primário',
            'secondary-button'  => 'Botão secundário',
            'alert'             => 'Esta é uma mensagem informativa.',
            'card-title'        => 'Título do cartão',
            'card-body'         => 'Este é um conteúdo de exemplo para mostrar como o texto e as superfícies aparecem com as cores selecionadas.',
            'link'              => 'Ver detalhes',
            'input-label'       => 'Texto do cabeçalho',
            'input-placeholder' => 'Campo de entrada',

            'nav' => [
                'dashboard' => 'Painel',
                'products'  => 'Produtos',
                'orders'    => 'Pedidos',
                'customers' => 'Clientes',
                'settings'  => 'Configurações',
            ],

            'status' => [
                'success' => 'Sucesso',
                'info'    => 'Informação',
                'warning' => 'Aviso',
                'danger'  => 'Perigo',
            ],
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Redefinir para o padrão',
        ],
    ],
];
