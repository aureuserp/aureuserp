<?php

return [
    'title' => 'Configurações de Ativos',

    'navigation' => [
        'title' => 'Ativos e etiquetagem',
        'group' => 'Ativos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Configuração Geral de Ativos',
                'description' => 'Gerencie a identificação de ativos, sequências de numeração e parâmetros operacionais.',
            ],
            'warehouses_and_operation' => [
                'title'       => 'Armazéns e Modelos Operacionais',
                'description' => 'Configure armazéns padrão e gerencie ativos fixos alocados em locais.',
            ],
            'custody_and_duration' => [
                'title'       => 'Regras de Custódia e Prazos',
                'description' => 'Defina períodos de custódia, datas previstas de devolução e restrições de alocação.',
            ],
            'governance_and_attachments' => [
                'title'       => 'Documentação e Governança de Baixas',
                'description' => 'Exija anexos obrigatórios para transições de custódia e controle baixas de ativos.',
            ],
            'consumables_rules' => [
                'title'       => 'Entrega de Consumíveis e Quantidades',
                'description' => 'Controle a entrega em lote, a seleção automática de destinatários, as quantidades padrão e as exigências de local ou anexos para consumíveis.',
            ],
        ],
        'fields' => [
            'tag-prefix'                              => 'Prefixo da Etiqueta do Ativo',
            'auto-generate-tag'                       => 'Gerar Etiqueta Automaticamente',
            'tag-padding'                             => 'Dígitos da Sequência da Etiqueta',
            'default-condition'                       => 'Condição Padrão do Ativo',
            'require-serial-by-default'               => 'Exigir Número de Série por Padrão',
            'default-warehouse'                       => 'Armazém Padrão',
            'enable-site-assets'                      => 'Habilitar Ativos de Local Fixo',
            'require-site-on-custody'                 => 'Exigir Local ao Entregar Custódia',
            'default-custody-duration-days'           => 'Duração Padrão da Custódia (Dias)',
            'require-expected-return-date'            => 'Exigir Data Prevista de Devolução',
            'prevent-duplicate-product-custody'       => 'Evitar Duplicação do Mesmo Produto',
            'require-attachments-on-custody'          => 'Exigir Anexos na Entrega de Custódia',
            'enable-consumables-batch-issue'          => 'Habilitar Entrega em Lote de Consumíveis',
            'auto-fill-consumable-employees-by-site'  => 'Preencher Destinatários pelo Local',
            'default-consumable-issue-quantity'       => 'Quantidade Padrão por Destinatário',
            'require-site-on-consumable-issue'        => 'Exigir Local na Entrega de Consumíveis',
            'require-attachments-on-consumable-issue' => 'Exigir Anexos na Entrega de Consumíveis',
            'require-attachments-on-damage'           => 'Exigir Anexos na Devolução Danificada',
            'require-attachments-on-termination'      => 'Exigir Anexos na Baixa do Ativo',
            'restrict-termination-to-managers'        => 'Restringir Baixa aos Gerentes',
            'termination-approval-cost-threshold'     => 'Limite de Custo para Aprovação de Baixa',
        ],
        'helpers' => [
            'tag-prefix'                              => 'Prefixo usado na geração automática de etiquetas (ex. AST-).',
            'tag-prefix-locked'                       => 'O prefixo da etiqueta está bloqueado porque já existem ativos registrados.',
            'auto-generate-tag'                       => 'Atribuir automaticamente etiquetas sequenciais aos novos ativos.',
            'tag-padding'                             => 'Número de dígitos do sequencial da etiqueta (3 a 8). Pré-visualização: :preview',
            'default-condition'                       => 'Condição inicial atribuída aos ativos recém-registrados.',
            'require-serial-by-default'               => 'Exigir número de série obrigatório no registro de unidades individuais.',
            'default-warehouse'                       => 'Armazém padrão atribuído aos ativos disponíveis se nenhum for especificado.',
            'manage-warehouses-link'                  => 'Gerenciar armazéns',
            'enable-site-assets'                      => 'Permitir alocar ativos diretamente em locais fixos sem um funcionário específico.',
            'manage-sites-link'                       => 'Gerenciar locais',
            'require-site-on-custody'                 => 'Exigir seleção de local ao entregar custódia a um funcionário.',
            'default-custody-duration-days'           => 'Número de dias adicionados à data de entrega para calcular a data prevista de devolução.',
            'require-expected-return-date'            => 'Exigir data prevista de devolução em todas as entregas a funcionários.',
            'prevent-duplicate-product-custody'       => 'Impedir a alocação de mais de um ativo do mesmo produto ao mesmo funcionário simultaneamente.',
            'require-attachments-on-custody'          => 'Exigir recibos de entrega ou notas de recebimento em emissões e transferências.',
            'enable-consumables-batch-issue'          => 'Permitir entregar um lote de consumíveis a vários destinatários em uma única operação.',
            'auto-fill-consumable-employees-by-site'  => 'Preencher a lista de destinatários com os funcionários ativos do local selecionado.',
            'default-consumable-issue-quantity'       => 'Quantidade deduzida por destinatário ao entregar consumíveis, de 1 a 10000.',
            'require-site-on-consumable-issue'        => 'Exigir seleção de local de custódia ao entregar consumíveis, independentemente das regras de ativos.',
            'require-attachments-on-consumable-issue' => 'Exigir comprovante de entrega ao emitir consumíveis, independentemente das regras de ativos.',
            'require-attachments-on-damage'           => 'Exigir fotos ou comprovantes ao devolver ativo em mau estado ou danificado.',
            'require-attachments-on-termination'      => 'Exigir documentação oficial ao sucatear ou registrar perda do ativo.',
            'restrict-termination-to-managers'        => 'Exigir permissões de gerente (Grupo ou Global) para sucatear ou marcar como perdido.',
            'termination-approval-cost-threshold'     => 'Ativos com custo inferior a este limite podem ser sucateados sem aprovação de gerente. Deixe em branco para todos os valores.',
        ],
    ],

    'warnings' => [
        'site-assets-held' => 'Não é possível desativar ativos de local enquanto houver ativos alocados em locais fixos.',
    ],
];
