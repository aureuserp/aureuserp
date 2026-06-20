<?php

return [
    'navigation' => [
        'title' => 'Campos personalizados',
        'group' => 'Configurações',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name' => 'Nome',
                    'code' => 'código',
                    'code-helper-text' => 'O código deve começar com uma letra ou sublinhado e pode conter apenas letras, números e sublinhados.',
                ],
            ],

            'options' => [
                'title' => 'Opções',

                'fields' => [
                    'add-option' => 'Adicionar opção',
                ],
            ],

            'form-settings' => [
                'title' => 'Configurações do formulário',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validações',

                        'fields' => [
                            'validation' => 'Validação',
                            'field' => 'Campo',
                            'value' => 'Valor',
                            'add-validation' => 'Adicionar validação',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Configurações adicionais',

                        'fields' => [
                            'setting' => 'Configuração',
                            'value' => 'Valor',
                            'color' => 'Cor',
                            'add-setting' => 'Adicionar configuração',

                            'color-options' => [
                                'danger' => 'Perigo',
                                'info' => 'Informação',
                                'primary' => 'Primário',
                                'secondary' => 'Secundário',
                                'warning' => 'Aviso',
                                'success' => 'Sucesso',
                            ],

                            'grid-options' => [
                                'row' => 'Linha',
                                'column' => 'Coluna',
                            ],

                            'input-modes' => [
                                'text' => 'Texto',
                                'email' => 'E-mail',
                                'numeric' => 'Numérico',
                                'integer' => 'Inteiro',
                                'password' => 'Senha',
                                'tel' => 'Telefone',
                                'url' => 'URL',
                                'color' => 'Cor',
                                'none' => 'Nenhum',
                                'decimal' => 'Decimal',
                                'search' => 'Pesquisar',
                                'url' => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt' => 'Maior que',
                        'gte' => 'Maior ou igual a',
                        'lt' => 'Menor que',
                        'lte' => 'Menor ou igual a',
                        'max-size' => 'Tamanho máximo',
                        'min-size' => 'Tamanho mínimo',
                        'multiple-of' => 'Múltiplo de',
                        'nullable' => 'Anulável',
                        'prohibited' => 'Proibido',
                        'prohibited-if' => 'Proibido se',
                        'prohibited-unless' => 'Proibido a menos que',
                        'prohibits' => 'Proíbe',
                        'required' => 'Obrigatório',
                        'required-if' => 'Obrigatório se',
                        'required-if-accepted' => 'Obrigatório se aceito',
                        'required-unless' => 'Obrigatório a menos que',
                        'required-with' => 'Obrigatório com',
                        'required-with-all' => 'Obrigatório com todos',
                        'required-without' => 'Obrigatório sem',
                        'required-without-all' => 'Obrigatório sem todos',
                        'rules' => 'Regras personalizadas',
                        'unique' => 'Único',
                    ],

                    'text' => [
                        'alpha-dash' => 'Alfa com traço',
                        'alpha-num' => 'Alfanumérico',
                        'ascii' => 'ASCII',
                        'doesnt-end-with' => 'Não termina com',
                        'doesnt-start-with' => 'Não começa com',
                        'ends-with' => 'Termina com',
                        'filled' => 'Preenchido',
                        'ip' => 'IP',
                        'ipv4' => 'IPv4',
                        'ipv6' => 'IPv6',
                        'length' => 'Comprimento',
                        'mac-address' => 'Endereço MAC',
                        'max-length' => 'Comprimento máximo',
                        'min-length' => 'Comprimento mínimo',
                        'regex' => 'Regex',
                        'starts-with' => 'Começa com',
                        'ulid' => 'ULID',
                        'uuid' => 'UUID',
                    ],

                    'textarea' => [
                        'filled' => 'Preenchido',
                        'max-length' => 'Comprimento máximo',
                        'min-length' => 'Comprimento mínimo',
                    ],

                    'select' => [
                        'different' => 'Diferente',
                        'exists' => 'Existe',
                        'in' => 'Em',
                        'not-in' => 'Não em',
                        'same' => 'Igual',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Aceito',
                        'declined' => 'Recusado',
                    ],

                    'toggle' => [
                        'accepted' => 'Aceito',
                        'declined' => 'Recusado',
                    ],

                    'checkbox-list' => [
                        'in' => 'Em',
                        'max-items' => 'Máximo de itens',
                        'min-items' => 'Mínimo de itens',
                    ],

                    'datetime' => [
                        'after' => 'Depois',
                        'after-or-equal' => 'Depois ou igual',
                        'before' => 'Antes',
                        'before-or-equal' => 'Antes ou igual',
                    ],

                    'editor' => [
                        'filled' => 'Preenchido',
                        'max-length' => 'Comprimento máximo',
                        'min-length' => 'Comprimento mínimo',
                    ],

                    'markdown' => [
                        'filled' => 'Preenchido',
                        'max-length' => 'Comprimento máximo',
                        'min-length' => 'Comprimento mínimo',
                    ],

                    'color' => [
                        'hex-color' => 'Cor hexadecimal',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize' => 'Capitalização automática',
                        'autocomplete' => 'Preenchimento automático',
                        'autofocus' => 'Foco automático',
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'input-mode' => 'Modo de entrada',
                        'mask' => 'Máscara',
                        'placeholder' => 'Placeholder',
                        'prefix' => 'Prefixo',
                        'prefix-icon' => 'Ícone do prefixo',
                        'prefix-icon-color' => 'Cor do ícone do prefixo',
                        'read-only' => 'Somente leitura',
                        'step' => 'Passo',
                        'suffix' => 'Sufixo',
                        'suffix-icon' => 'Ícone do sufixo',
                        'suffix-icon-color' => 'Cor do ícone do sufixo',
                    ],

                    'textarea' => [
                        'autofocus' => 'Foco automático',
                        'autosize' => 'Tamanho automático',
                        'cols' => 'Colunas',
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helperText' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hintColor' => 'Cor da dica',
                        'hintIcon' => 'Ícone da dica',
                        'id' => 'Id',
                        'placeholder' => 'Placeholder',
                        'read-only' => 'Somente leitura',
                        'rows' => 'Linhas',
                    ],

                    'select' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'loading-message' => 'Mensagem de carregamento',
                        'no-search-results-message' => 'Mensagem de nenhum resultado de busca',
                        'options-limit' => 'Limite de opções',
                        'preload' => 'Pré-carregar',
                        'searchable' => 'Pesquisável',
                        'search-debounce' => 'Atraso da busca',
                        'searching-message' => 'Mensagem de busca',
                        'search-prompt' => 'Prompt de busca',
                    ],

                    'radio' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                    ],

                    'checkbox' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'inline' => 'Em linha',
                    ],

                    'toggle' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'off-color' => 'Cor desligada',
                        'off-icon' => 'Ícone desligado',
                        'on-color' => 'Cor ligada',
                        'on-icon' => 'Ícone ligado',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable' => 'Alternável em massa',
                        'columns' => 'Colunas',
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'grid-direction' => 'Direção da grade',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'max-items' => 'Máximo de itens',
                        'min-items' => 'Mínimo de itens',
                        'no-search-results-message' => 'Mensagem de nenhum resultado de busca',
                        'searchable' => 'Pesquisável',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Fechar ao selecionar data',
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'disabled-dates' => 'Datas desabilitadas',
                        'display-format' => 'Formato de exibição',
                        'first-fay-of-week' => 'Primeiro dia da semana',
                        'format' => 'Formato',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'hours-step' => 'Passo das horas',
                        'id' => 'Id',
                        'locale' => 'Localidade',
                        'minutes-step' => 'Passo dos minutos',
                        'seconds' => 'Segundos',
                        'seconds-step' => 'Passo dos segundos',
                        'timezone' => 'Fuso horário',
                        'week-starts-on-monday' => 'Semana começa na segunda-feira',
                        'week-starts-on-sunday' => 'Semana começa no domingo',
                    ],

                    'editor' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'placeholder' => 'Placeholder',
                        'read-only' => 'Somente leitura',
                    ],

                    'markdown' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'id' => 'Id',
                        'placeholder' => 'Placeholder',
                        'read-only' => 'Somente leitura',
                    ],

                    'color' => [
                        'default' => 'Valor padrão',
                        'disabled' => 'Desabilitado',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                        'hsl' => 'HSL',
                        'id' => 'Id',
                        'rgb' => 'RGB',
                        'rgba' => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types' => 'Tipos de arquivo aceitos',
                        'append-files' => 'Anexar arquivos',
                        'deletable' => 'Excluível',
                        'directory' => 'Diretório',
                        'downloadable' => 'Baixável',
                        'fetch-file-information' => 'Buscar informações do arquivo',
                        'file-attachments-directory' => 'Diretório de anexos de arquivo',
                        'file-attachments-visibility' => 'Visibilidade dos anexos de arquivo',
                        'image' => 'Imagem',
                        'image-crop-aspect-ratio' => 'Proporção de corte da imagem',
                        'image-editor' => 'Editor de imagem',
                        'image-editor-aspect-ratios' => 'Proporções do editor de imagem',
                        'image-editor-empty-fill-color' => 'Cor de preenchimento vazio do editor de imagem',
                        'image-editor-mode' => 'Modo do editor de imagem',
                        'image-preview-height' => 'Altura da pré-visualização da imagem',
                        'image-resize-mode' => 'Modo de redimensionamento da imagem',
                        'image-resize-target-height' => 'Altura alvo de redimensionamento da imagem',
                        'image-resize-target-width' => 'Largura alvo de redimensionamento da imagem',
                        'loading-indicator-position' => 'Posição do indicador de carregamento',
                        'move-files' => 'Mover arquivos',
                        'openable' => 'Abrível',
                        'orient-images-from-exif' => 'Orientar imagens pelo EXIF',
                        'panel-aspect-ratio' => 'Proporção do painel',
                        'panel-layout' => 'Layout do painel',
                        'previewable' => 'Pré-visualizável',
                        'remove-uploaded-file-button-position' => 'Posição do botão remover arquivo enviado',
                        'reorderable' => 'Reordenável',
                        'store-files' => 'Armazenar arquivos',
                        'upload-button-position' => 'Posição do botão de upload',
                        'uploading-message' => 'Mensagem de upload',
                        'upload-progress-indicator-position' => 'Posição do indicador de progresso do upload',
                        'visibility' => 'Visibilidade',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Configurações da tabela',

                'fields' => [
                    'use-in-table' => 'Usar na tabela',
                    'setting' => 'Configuração',
                    'value' => 'Valor',
                    'color' => 'Cor',
                    'alignment' => 'Alinhamento',
                    'font-weight' => 'Peso da fonte',
                    'icon-position' => 'Posição do ícone',
                    'size' => 'Tamanho',
                    'add-setting' => 'Adicionar configuração',

                    'color-options' => [
                        'danger' => 'Perigo',
                        'info' => 'Informação',
                        'primary' => 'Primário',
                        'secondary' => 'Secundário',
                        'warning' => 'Aviso',
                        'success' => 'Sucesso',
                    ],

                    'alignment-options' => [
                        'start' => 'Início',
                        'left' => 'Esquerda',
                        'center' => 'Centro',
                        'end' => 'Fim',
                        'right' => 'Direita',
                        'justify' => 'Justificar',
                        'between' => 'Entre',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra leve',
                        'light' => 'Leve',
                        'normal' => 'Normal',
                        'medium' => 'Médio',
                        'semi-bold' => 'Seminegrito',
                        'bold' => 'Negrito',
                        'extra-bold' => 'Extra negrito',
                    ],

                    'icon-position-options' => [
                        'before' => 'Antes',
                        'after' => 'Depois',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra pequeno',
                        'small' => 'Pequeno',
                        'medium' => 'Médio',
                        'large' => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end' => 'Alinhar ao fim',
                        'alignment' => 'Alinhamento',
                        'align-start' => 'Alinhar ao início',
                        'badge' => 'Badge',
                        'boolean' => 'Booleano',
                        'color' => 'Cor',
                        'copyable' => 'Copiável',
                        'copy-message' => 'Mensagem de cópia',
                        'copy-message-duration' => 'Duração da mensagem de cópia',
                        'default' => 'Padrão',
                        'filterable' => 'Filtrável',
                        'groupable' => 'Agrupável',
                        'grow' => 'Expandir',
                        'icon' => 'Ícone',
                        'icon-color' => 'Cor do ícone',
                        'icon-position' => 'Posição do ícone',
                        'label' => 'Rótulo',
                        'limit' => 'Limite',
                        'line-clamp' => 'Limite de linhas',
                        'money' => 'Dinheiro',
                        'placeholder' => 'Placeholder',
                        'prefix' => 'Prefixo',
                        'searchable' => 'Pesquisável',
                        'size' => 'Tamanho',
                        'sortable' => 'Ordenável',
                        'suffix' => 'Sufixo',
                        'toggleable' => 'Alternável',
                        'tooltip' => 'Tooltip',
                        'vertical-alignment' => 'Alinhamento vertical',
                        'vertically-align-start' => 'Alinhar verticalmente ao início',
                        'weight' => 'Peso',
                        'width' => 'Largura',
                        'words' => 'Palavras',
                        'wrap-header' => 'Quebrar cabeçalho',
                        'column-span' => 'Extensão da coluna',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                    ],

                    'datetime' => [
                        'date' => 'Data',
                        'date-time' => 'Data e hora',
                        'date-time-tooltip' => 'Tooltip de data e hora',
                        'since' => 'Desde',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Configurações da lista de informações',

                'fields' => [
                    'setting' => 'Configuração',
                    'value' => 'Valor',
                    'color' => 'Cor',
                    'font-weight' => 'Peso da fonte',
                    'icon-position' => 'Posição do ícone',
                    'size' => 'Tamanho',
                    'add-setting' => 'Adicionar configuração',

                    'color-options' => [
                        'danger' => 'Perigo',
                        'info' => 'Informação',
                        'primary' => 'Primário',
                        'secondary' => 'Secundário',
                        'warning' => 'Aviso',
                        'success' => 'Sucesso',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra leve',
                        'light' => 'Leve',
                        'normal' => 'Normal',
                        'medium' => 'Médio',
                        'semi-bold' => 'Seminegrito',
                        'bold' => 'Negrito',
                        'extra-bold' => 'Extra negrito',
                    ],

                    'icon-position-options' => [
                        'before' => 'Antes',
                        'after' => 'Depois',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra pequeno',
                        'small' => 'Pequeno',
                        'medium' => 'Médio',
                        'large' => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end' => 'Alinhar ao fim',
                        'alignment' => 'Alinhamento',
                        'align-start' => 'Alinhar ao início',
                        'badge' => 'Badge',
                        'boolean' => 'Booleano',
                        'color' => 'Cor',
                        'copyable' => 'Copiável',
                        'copy-message' => 'Mensagem de cópia',
                        'copy-message-duration' => 'Duração da mensagem de cópia',
                        'default' => 'Padrão',
                        'filterable' => 'Filtrável',
                        'groupable' => 'Agrupável',
                        'grow' => 'Expandir',
                        'icon' => 'Ícone',
                        'icon-color' => 'Cor do ícone',
                        'icon-position' => 'Posição do ícone',
                        'label' => 'Rótulo',
                        'limit' => 'Limite',
                        'line-clamp' => 'Limite de linhas',
                        'money' => 'Dinheiro',
                        'placeholder' => 'Placeholder',
                        'prefix' => 'Prefixo',
                        'searchable' => 'Pesquisável',
                        'size' => 'Tamanho',
                        'sortable' => 'Ordenável',
                        'suffix' => 'Sufixo',
                        'toggleable' => 'Alternável',
                        'tooltip' => 'Tooltip',
                        'vertical-alignment' => 'Alinhamento vertical',
                        'vertically-align-start' => 'Alinhar verticalmente ao início',
                        'weight' => 'Peso',
                        'width' => 'Largura',
                        'words' => 'Palavras',
                        'wrap-header' => 'Quebrar cabeçalho',
                        'column-span' => 'Extensão da coluna',
                        'helper-text' => 'Texto de ajuda',
                        'hint' => 'Dica',
                        'hint-color' => 'Cor da dica',
                        'hint-icon' => 'Ícone da dica',
                    ],

                    'datetime' => [
                        'date' => 'Data',
                        'date-time' => 'Data e hora',
                        'date-time-tooltip' => 'Tooltip de data e hora',
                        'since' => 'Desde',
                    ],

                    'checkbox-list' => [
                        'separator' => 'Separador',
                        'list-with-line-breaks' => 'Lista com quebras de linha',
                        'bulleted' => 'Com marcadores',
                        'limit-list' => 'Limitar lista',
                        'expandable-limited-list' => 'Lista limitada expansível',
                    ],

                    'select' => [
                        'separator' => 'Separador',
                        'list-with-line-breaks' => 'Lista com quebras de linha',
                        'bulleted' => 'Com marcadores',
                        'limit-list' => 'Limitar lista',
                        'expandable-limited-list' => 'Lista limitada expansível',
                    ],

                    'checkbox' => [
                        'boolean' => 'Booleano',
                        'false-icon' => 'Ícone falso',
                        'true-icon' => 'Ícone verdadeiro',
                        'true-color' => 'Cor verdadeira',
                        'false-color' => 'Cor falsa',
                    ],

                    'toggle' => [
                        'boolean' => 'Booleano',
                        'false-icon' => 'Ícone falso',
                        'true-icon' => 'Ícone verdadeiro',
                        'true-color' => 'Cor verdadeira',
                        'false-color' => 'Cor falsa',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'type' => 'Tipo',
                    'input-type' => 'Tipo de entrada',
                    'is-multiselect' => 'É multisseleção',
                    'sort-order' => 'Ordem de classificação',

                    'type-options' => [
                        'text' => 'Entrada de texto',
                        'textarea' => 'Área de texto',
                        'select' => 'Seleção',
                        'checkbox' => 'Caixa de seleção',
                        'radio' => 'Rádio',
                        'toggle' => 'Alternador',
                        'checkbox-list' => 'Lista de caixas de seleção',
                        'datetime' => 'Seletor de data e hora',
                        'editor' => 'Editor de texto rico',
                        'markdown' => 'Editor Markdown',
                        'color' => 'Seletor de cor',
                    ],

                    'input-type-options' => [
                        'text' => 'Texto',
                        'email' => 'E-mail',
                        'numeric' => 'Numérico',
                        'integer' => 'Inteiro',
                        'password' => 'Senha',
                        'tel' => 'Telefone',
                        'url' => 'URL',
                        'color' => 'Cor',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Recurso',

                'fields' => [
                    'resource' => 'Recurso',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code' => 'Código',
            'name' => 'Nome',
            'type' => 'Tipo',
            'resource' => 'Recurso',
            'created-at' => 'Criado em',
        ],

        'groups' => [
        ],

        'filters' => [
            'type' => 'Tipo',
            'resource' => 'Recurso',

            'type-options' => [
                'text' => 'Entrada de texto',
                'textarea' => 'Área de texto',
                'select' => 'Seleção',
                'checkbox' => 'Caixa de seleção',
                'radio' => 'Rádio',
                'toggle' => 'Alternador',
                'checkbox-list' => 'Lista de caixas de seleção',
                'datetime' => 'Seletor de data e hora',
                'editor' => 'Editor de texto rico',
                'markdown' => 'Editor Markdown',
                'color' => 'Seletor de cor',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Campo restaurado',
                    'body' => 'O campo foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Campo excluído',
                    'body' => 'O campo foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Campo excluído permanentemente',
                    'body' => 'O campo foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Campos restaurados',
                    'body' => 'Os campos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Campos excluídos',
                    'body' => 'Os campos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Campos excluídos permanentemente',
                    'body' => 'Os campos foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
