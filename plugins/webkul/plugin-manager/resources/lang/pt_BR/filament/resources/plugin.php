<?php

return [

    'navigation' => [
        'group' => 'Plugins',
    ],

    'title' => 'Plugin',

    'table' => [
        'version' => 'Versão',
        'dependencies' => 'Dependências',
        'dependencies_suffix' => ' dependências',
    ],

    'status' => [
        'installed' => 'Instalado',
        'not_installed' => 'Não instalado',
    ],

    'filters' => [
        'installation_status' => 'Status de instalação',
        'all_plugins' => 'Todos os plugins',
        'installed' => 'Instalado',
        'not_installed' => 'Não instalado',
        'active_status' => 'Status ativo',
        'author' => 'Autor',
        'webkul' => 'Webkul',
        'third_party' => 'Terceiros',
    ],

    'actions' => [
        'install' => [
            'title' => 'Instalar',
            'heading' => 'Instalar plugin :name',
            'description' => 'Tem certeza de que deseja instalar o plugin \':name\'? Isso executará migrations e seeders.',
            'submit' => 'Instalar plugin',
        ],
        'uninstall' => [
            'title' => 'Desinstalar',
            'heading' => 'Desinstalar plugin',
            'submit' => 'Desinstalar plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin instalado com sucesso',
            'body' => 'O plugin \':name\' foi instalado.',
        ],
        'installed-failed' => [
            'title' => 'Falha na instalação',
        ],
        'uninstalled' => [
            'title' => 'Plugin desinstalado com sucesso',
            'body' => 'O plugin \':name\' foi desinstalado.',
        ],
        'uninstalled-failed' => [
            'title' => 'Falha na desinstalação',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin' => ' Informações do plugin',
            'dependencies' => 'Dependências',
        ],
        'name' => 'Nome do plugin',
        'version' => 'Versão',
        'dependencies' => 'Plugins obrigatórios',
        'dependents' => 'Plugins que dependem deste',
        'is_installed' => 'Status de instalação',
        'license' => 'Licença',
        'summary' => 'Descrição',

        'dependencies-repeater' => [
            'title' => 'Plugins obrigatórios',
            'name' => 'Nome do plugin',
            'is_installed' => 'Instalado',
            'placeholder' => 'Nenhuma dependência obrigatória',
        ],

        'dependents-repeater' => [
            'title' => 'Plugins que dependem deste',
            'name' => 'Nome do plugin',
            'is_installed' => 'Instalado',
            'placeholder' => 'Nenhum dependente',
        ],

    ],

];
