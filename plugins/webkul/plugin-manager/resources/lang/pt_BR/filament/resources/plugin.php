<?php

return [

    'navigation' => [
        'group' => 'Plugins',
    ],

    'title' => 'Plugin',

    'table' => [
        'version'             => 'Versão',
        'dependencies'        => 'Dependências',
        'dependencies_suffix' => ' dependências',
    ],

    'status' => [
        'installed'     => 'Instalado',
        'not_installed' => 'Não instalado',
    ],

    'filters' => [
        'installation_status' => 'Status de instalação',
        'all_plugins'         => 'Todos os plugins',
        'installed'           => 'Instalado',
        'not_installed'       => 'Não instalado',
        'active_status'       => 'Status ativo',
        'author'              => 'Autor',
        'webkul'              => 'Webkul',
        'third_party'         => 'Terceiros',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Instalar',
            'heading'     => 'Instalar plugin :name',
            'description' => "Are you sure you want to install the ':name' plugin? This will run migrations and seeders.",
            'submit'      => 'Instalar plugin',
        ],
        'uninstall' => [
            'title'      => 'Desinstalar',
            'heading'    => 'Desinstalar plugin',
            'submit'     => 'Desinstalar plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin instalado com sucesso',
            'body'  => "The ':name' plugin has been installed.",
        ],
        'installed-failed' => [
            'title' => 'Falha na instalação',
        ],
        'uninstalled' => [
            'title' => 'Plugin desinstalado com sucesso',
            'body'  => "The ':name' plugin has been uninstalled.",
        ],
        'uninstalled-failed' => [
            'title' => 'Falha na desinstalação',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Informações do plugin',
            'dependencies' => 'Dependências',
        ],
        'name'         => 'Nome do plugin',
        'author'       => 'Autor',
        'version'      => 'Versão',
        'dependencies' => 'Plugins obrigatórios',
        'dependents'   => 'Plugins que dependem deste',
        'is_installed' => 'Status de instalação',
        'license'      => 'Licença',
        'summary'      => 'Descrição',

        'dependencies-repeater' => [
            'title'        => 'Plugins obrigatórios',
            'name'         => 'Nome do plugin',
            'is_installed' => 'Instalado',
            'placeholder'  => 'Nenhuma dependência obrigatória',
        ],

        'dependents-repeater' => [
            'title'        => 'Plugins que dependem deste',
            'name'         => 'Nome do plugin',
            'is_installed' => 'Instalado',
            'placeholder'  => 'Nenhum dependente',
        ],

    ],

    'names' => [
        'accounting'     => 'Contabilidade',
        'accounts'       => 'Contas',
        'analytics'      => 'Análises',
        'barcode'        => 'Código de barras',
        'blogs'          => 'Blogs',
        'chatter'        => 'Chatter',
        'contacts'       => 'Contatos',
        'employees'      => 'Colaboradores',
        'fields'         => 'Campos personalizados',
        'full-calendar'  => 'Calendário',
        'inventories'    => 'Estoque',
        'invoices'       => 'Faturas',
        'maintenance'    => 'Manutenção',
        'manufacturing'  => 'Produção',
        'partners'       => 'Parceiros',
        'payments'       => 'Pagamentos',
        'plugin-manager' => 'Gerenciador de plugins',
        'products'       => 'Produtos',
        'projects'       => 'Projetos',
        'purchases'      => 'Compras',
        'recruitments'   => 'Recrutamento',
        'sales'          => 'Vendas',
        'security'       => 'Segurança',
        'support'        => 'Suporte',
        'table-views'    => 'Visualizações de tabela',
        'time-off'       => 'Ausências',
        'timesheets'     => 'Apontamentos de horas',
        'website'        => 'Site',
    ],

    'summaries' => [
        'accounting'     => 'Gerencie plano de contas, diários e lançamentos financeiros',
        'accounts'       => 'Gerenciamento central de contas e configurações financeiras',
        'analytics'      => 'Relatórios e dashboards para insights do negócio',
        'barcode'        => 'App de operações por código de barras para estoque e produção',
        'blogs'          => 'Gerencie blogs',
        'chatter'        => 'Registro de atividades, mensagens e acompanhamentos em registros',
        'contacts'       => 'Gerenciamento de contatos para clientes e fornecedores',
        'employees'      => 'Gerenciamento de colaboradores',
        'fields'         => 'Adicione campos personalizados aos recursos',
        'full-calendar'  => 'Visualizações de calendário e agendamento de eventos',
        'inventories'    => 'Gerenciamento de estoque e armazéns',
        'invoices'       => 'Geração e gerenciamento de faturas',
        'maintenance'    => 'Gerenciamento de manutenção',
        'manufacturing'  => 'Gerenciamento de produção e manufatura',
        'partners'       => 'Gerencie parceiros comerciais',
        'payments'       => 'Gerencie pagamentos e transações',
        'plugin-manager' => 'Gerenciador de plugins para o Aureus ERP',
        'products'       => 'Catálogo de produtos e gerenciamento de variantes',
        'projects'       => 'Planejamento e gerenciamento de projetos',
        'purchases'      => 'Gerenciamento de compras e pedidos de compra',
        'recruitments'   => 'Acompanhamento de candidatos e contratações',
        'sales'          => 'Pipeline de vendas e gerenciamento de oportunidades',
        'security'       => 'Funções, permissões e controle de acesso',
        'support'        => 'Suporte ao cliente e chamados',
        'table-views'    => 'Visualizações de tabela salvas e personalizáveis',
        'time-off'       => 'Gerenciamento e acompanhamento de ausências',
        'timesheets'     => 'Acompanhamento de horas trabalhadas dos colaboradores',
        'website'        => 'Site para clientes',
    ],

];
