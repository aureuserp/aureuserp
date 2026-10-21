<?php

return [
    'common' => [
        'ok'      => 'Ok',
        'close'   => 'Fechar',
        'cancel'  => 'Cancelar',
        'save'    => 'Salvar',
        'saving'  => 'Salvando…',
        'discard' => 'Descartar',
        'clear'   => 'Limpar',
        'apply'   => 'Aplicar',
        'open'    => 'Abrir',
        'resume'  => 'Retomar',
        'confirm' => 'Confirmar',
        'back'    => 'Voltar',
        'print'   => 'Imprimir',
        'new'     => 'Novo',
        'more'    => '+ :count a mais',
    ],

    'nav' => [
        'home'      => 'Início',
        'orders'    => 'Pedidos',
        'registers' => 'Caixas',
    ],

    'picker' => [
        'title'       => 'Caixas',
        'open'        => 'Abrir caixa',
        'resume'      => 'Retomar',
        'discard'     => 'Descartar sessão',
        'open-by'     => 'Aberto por :name',
        'closed'      => 'Fechado',
        'opening'     => 'Abrindo…',
        'back-office' => 'Back office',
        'logout'      => 'Sair',
        'offline'     => 'Conecte-se à rede para abrir um caixa.',
        'failed'      => 'Não foi possível acessar o servidor. Tente novamente.',

        'discard-confirm' => [
            'heading' => 'Descartar esta sessão?',
            'confirm' => 'Descartar',
        ],

        'empty' => [
            'heading'     => 'Nenhum ponto de venda disponível',
            'description' => 'Crie um ponto de venda ativo no back office para começar a vender.',
        ],
    ],

    'history' => [
        'heading'       => 'Pedidos',
        'back'          => 'Voltar',
        'search'        => 'Pesquisar por pedido, recibo, cliente ou mesa',
        'empty'         => 'Nenhum pedido corresponde a essa pesquisa.',
        'select-order'  => 'Selecione um pedido para ver os detalhes.',
        'walk-in'       => 'Cliente avulso',
        'invoice'       => 'Fatura',
        'invoiced'      => 'Faturado',
        'print-invoice' => 'Imprimir fatura',
        'cancel'        => 'Cancelar pedido',
        'print'         => 'Imprimir recibo',
        'details'       => 'Detalhes',
        'refund'        => 'Reembolso',
        'refund-prompt' => 'Selecione o(s) produto(s) a reembolsar e defina a quantidade.',
        'to-refund'     => 'A reembolsar',
        'refundable'    => 'Reembolsável: :qty',
        'taxes'         => 'Impostos',
        'total'         => 'Total',
        'paid'          => 'Pago',
        'lines'         => 'Linhas',
        'payments'      => 'Pagamentos',
        'prev'          => 'Anterior',
        'next'          => 'Próximo',
        'page'          => 'Página :page de :total',
        'unreachable'   => 'Não foi possível acessar o servidor. Verifique a conexão e tente novamente.',
        'failed'        => 'A solicitação falhou (:status).',

        'invoice-customer' => [
            'heading'        => 'É necessário um cliente para faturar',
            'question'       => 'Deseja abrir a lista de clientes para selecionar um?',
            'select-heading' => 'Selecione um cliente para faturar',
        ],

        'columns' => [
            'date'     => 'Data',
            'receipt'  => 'Recibo',
            'order'    => 'Pedido',
            'customer' => 'Cliente',
            'cashier'  => 'Operador de caixa',
            'tracking' => 'Rastreamento',
            'table'    => 'Mesa',
            'total'    => 'Total',
            'status'   => 'Status',
        ],

        'status' => [
            'active'   => 'Ativo',
            'draft'    => 'Em andamento',
            'paid'     => 'Pago',
            'done'     => 'Lançado',
            'invoiced' => 'Faturado',
            'canceled' => 'Cancelado',
        ],
    ],

    'parked' => [
        'walk-in'         => 'Cliente avulso',
        'items'           => '{1} :count item|[2,*] :count itens',
        'count'           => ':count em espera',
        'view-all'        => 'Ver tudo',
        'heading'         => 'Pedidos em espera',
        'search'          => 'Buscar pedidos por nome, referência ou produto',
        'no-match'        => 'Nenhum pedido corresponde a essa busca.',
        'parked-ago'      => 'em espera :time',
        'discard'         => 'Descartar pedido',
        'discard-confirm' => [
            'heading'     => 'Descartar o pedido em espera?',
            'description' => 'As linhas dele serão perdidas. Esta ação não pode ser desfeita.',
        ],

        'empty' => [
            'heading'     => 'Nada em espera',
            'description' => 'Os pedidos que você deixar de lado ficarão aqui.',
        ],
    ],

    'opening-control' => [
        'heading'                  => 'Controle de abertura',
        'cash'                     => 'Dinheiro de abertura',
        'previous-closing-balance' => 'Saldo de fechamento da sessão anterior: :amount',
        'note'                     => 'Observação de abertura',
        'placeholder'              => 'Adicione uma observação de abertura…',
        'confirm'                  => 'Abrir caixa',
        'cancel'                   => 'Cancelar',
    ],

    'tabs' => [
        'new'      => 'Novo pedido',
        'all'      => 'Todos os pedidos',
        'view-all' => 'Ver tudo',
    ],

    'menu' => [
        'label'          => 'Menu',
        'orders'         => 'Pedidos',
        'cash-in-out'    => 'Entrada / Saída de caixa',
        'create-product' => 'Criar produto',
        'back-office'    => 'Back office',
        'close-register' => 'Fechar caixa',
        'install'        => 'Instalar aplicativo',
        'theme'          => 'Alternar tema',
        'logout'         => 'Sair',
    ],

    'cash-movement' => [
        'heading' => 'Entrada / saída de caixa',
        'in'      => 'Entrada de caixa',
        'out'     => 'Saída de caixa',
        'amount'  => 'Valor',
        'reason'  => 'Motivo',
        'confirm' => 'Registrar movimento',
        'close'   => 'Fechar',

        'notification' => [
            'title' => 'Movimento de caixa registrado',

            'body' => [
                'in'  => ':amount adicionado ao caixa.',
                'out' => ':amount retirado do caixa.',
            ],
        ],
    ],

    'closing' => [
        'copy'                     => 'Copiar o valor esperado',
        'heading'                  => 'Fechando o caixa',
        'expected'                 => 'Esperado na gaveta',
        'counted'                  => 'Contado',
        'note'                     => 'Observação de fechamento',
        'confirm'                  => 'Fechar caixa',
        'back'                     => 'Voltar ao PDV',
        'method'                   => 'Método de pagamento',
        'total'                    => 'Total',
        'opening'                  => 'Abertura',
        'payments'                 => 'Pagamentos em dinheiro',
        'moves'                    => 'Entrada / saída de caixa',
        'cash-in'                  => 'Entrada de caixa :number',
        'cash-out'                 => 'Saída de caixa :number',
        'orders'                   => ':quantity pedidos',
        'difference'               => 'Diferença',
        'count'                    => 'Contagem de dinheiro',
        'clear'                    => 'Limpar',
        'discard'                  => 'Descartar',
        'daily-sale'               => 'Venda do dia',
        'opening-note'             => 'Observação de abertura',
        'authorized-diff'          => 'Diferença máxima permitida: :amount',
        'authorized-diff-exceeded' => 'A diferença está acima do limite permitido. Somente um gerente pode fechar este caixa.',
        'unbalanced'               => 'O lançamento de fechamento está desbalanceado em :amount. Um gerente deve selecionar uma conta de balanceamento para fechar.',
    ],

    'draft-orders' => [
        'heading'       => 'Erro',
        'message'       => 'Você não pode fechar o PDV enquanto houver pedidos em rascunho.',
        'review-orders' => 'Revisar pedidos',
        'cancel-orders' => 'Cancelar pedidos',
    ],

    'product-form' => [
        'heading'             => 'Novo produto',
        'name'                => 'Nome do produto',
        'name-placeholder'    => 'ex.: X-Burger',
        'barcode'             => 'Código de barras',
        'barcode-placeholder' => 'ex.: 1234567890',
        'tracking'            => 'Controlar estoque',
        'price'               => 'Preço de venda',
        'taxes'               => 'Impostos de venda',
        'tax-included'        => '(= :amount com impostos)',
        'category'            => 'Categoria do PDV',
        'unsaleable'          => 'Não vendável',

        'notification' => [
            'title' => 'Produto criado',
        ],

        'error' => [
            'failed'  => 'Não foi possível criar o produto (:status)',
            'offline' => 'Criar um produto exige conexão.',
        ],
    ],

    'product-info' => [
        'heading'          => 'Informações do produto',
        'inventory'        => 'Estoque',
        'available'        => 'disponíveis neste caixa',
        'negative-warning' => 'A venda continua permitida; o estoque ficará negativo e o back office mostrará a falta.',
        'financials'       => 'Financeiro',
        'price'            => 'Preço',
        'cost'             => 'Custo',
        'margin'           => 'Margem',
        'order'            => 'Neste pedido',
        'quantity'         => 'Quantidade',
        'total-price'      => 'Preço total',
        'total-margin'     => 'Margem total',
        'add'              => 'Adicionar ao pedido',
    ],

    'customers' => [
        'heading'      => 'Selecionar um cliente',
        'search'       => 'Buscar clientes',
        'no-match'     => 'Nenhum cliente corresponde a essa busca. Somente os clientes carregados no início da sessão podem ser buscados offline.',
        'clear'        => 'Remover cliente',
        'badge-new'    => 'novo',

        'create' => [
            'label'   => 'Novo cliente',
            'heading' => 'Novo cliente',
            'created' => ':name adicionado e selecionado.',

            'fields' => [
                'name'  => 'Nome',
                'email' => 'E-mail',
                'phone' => 'Telefone',
            ],
        ],
    ],

    'variants' => [
        'heading'     => 'Seleção de atributos',
        'confirm'     => 'Adicionar',
        'unavailable' => 'Esta combinação não existe.',
    ],

    'lots' => [
        'heading'        => 'Número(s) de lote/série obrigatório(s)',
        'placeholder'    => 'Número de série/lote',
        'add'            => 'Adicionar',
        'remove'         => 'Remover número',
        'missing'        => 'Definir número de lote / série',
        'none-available' => 'Não há número de série/lote para o produto selecionado, e a criação deles não é permitida pelo aplicativo do Ponto de venda.',

        'warning' => [
            'heading' => 'Faltam alguns números de série/lote',
            'body'    => "Você está tentando vender produtos com números de série/lote, mas alguns deles não foram informados.\nDeseja continuar mesmo assim?",
            'proceed' => 'Ok',
        ],
    ],

    'notes' => [
        'internal'    => 'Observação interna',
        'kitchen'     => 'Observação da cozinha',
        'heading'     => 'Adicionar observação interna',
        'empty'       => 'Nenhum modelo de observação configurado.',
        'hint'        => 'Escolha primeiro uma linha e depois uma observação.',
        'placeholder' => 'Adicione uma observação para esta linha',
    ],

    'money-details' => [
        'label'            => 'Moedas/Cédulas',
        'opening-heading'  => 'Detalhes de abertura:',
        'closing-heading'  => 'Detalhes de fechamento:',
        'total'            => 'Total: :total',
        'confirm'          => 'Confirmar',
        'close'            => 'Fechar',
        'increase'         => 'Adicionar um',
        'decrease'         => 'Remover um',
    ],

    'cart' => [
        'discount' => ':percentage% de desconto',
        'subtotal' => 'Subtotal',
        'tax'      => 'Impostos',
        'rounding' => 'Arredondamento',
        'total'    => 'Total',
        'remove'   => 'Remover :product',

        'empty' => [
            'description' => 'Escaneie ou toque em um produto para começar',
        ],
    ],

    'catalogue' => [
        'search'          => 'Buscar produtos',
        'create-product'  => 'Criar produto',
        'info'            => 'Informações do produto :product',
        'info-depleted'   => 'Informações do produto :product, nenhum disponível',
    ],

    'numpad' => [
        'qty'       => 'Qtd.',
        'price'     => 'Preço',
        'backspace' => 'Retrocesso',
        'clear'     => 'C',
    ],

    'payment' => [
        'ship-later'                            => 'Enviar depois',
        'ship-later-heading'                    => 'Selecione a data de envio',
        'ship-later-error-heading'              => 'Endereço incorreto para envio',
        'ship-later-no-customer'                => 'Selecione um cliente antes de enviar este pedido depois.',
        'ship-later-no-address'                 => 'O cliente selecionado precisa de um endereço.',
        'select-method'                         => 'Selecione um método de pagamento',
        'invoice'                               => 'Fatura',
        'change'                                => 'troco',
        'remove'                                => 'Remover o pagamento :method',
        'validate'                              => 'Validar',
        'max-value-title'                       => 'Valor máximo atingido',
        'max-value-body'                        => 'O valor não pode ser maior que o valor devido se você não tiver um método de pagamento em dinheiro configurado.',
        'customer-required-title'               => 'Cliente obrigatório',
        'customer-required-body-terminal'       => 'Selecione um cliente antes de validar este pedido; este terminal exige um.',
        'customer-required-body-payment-method' => 'Selecione um cliente antes de validar este pedido; o método de pagamento selecionado exige um.',
        'customer-required-body-invoice'        => 'Selecione um cliente antes de validar este pedido para gerar uma fatura.',
        'incomplete-title'                      => 'Método de pagamento necessário',
        'incomplete-body'                       => 'Selecione um método de pagamento que cubra o total antes de validar este pedido.',
    ],

    'floor' => [
        'back'      => 'Planta',
        'table'     => 'Mesa :table',
        'no-table'  => 'Sem mesa',
        'empty'     => 'Ainda não há mesas neste andar. Adicione-as no back office.',
        'no-floors' => 'Nenhum andar disponível. Adicione um novo andar para começar.',
        'seats'     => '{1} :count lugar|[2,*] :count lugares',
        'orders'    => '{1} :count pedido|[2,*] :count pedidos',
        'guests'    => '{1} :count cliente|[2,*] :count clientes',
    ],

    'guests' => [
        'label'     => 'Clientes',
        'heading'   => 'Número de clientes',
        'increase'  => 'Adicionar cliente',
        'decrease'  => 'Remover cliente',
        'per-guest' => ':amount por cliente',
    ],

    'split' => [
        'label'    => 'Dividir',
        'heading'  => 'Dividir a conta',
        'hint'     => 'Toque em uma linha para mover uma unidade para a nova conta. Toque novamente para mover mais.',
        'new-bill' => 'Nova conta',
        'confirm'  => 'Dividir pedido',
    ],

    'bill' => [
        'label'   => 'Conta',
        'heading' => 'Impressão da conta',
    ],

    'takeaway' => [
        'badge'       => 'Para viagem',
        'to-takeaway' => 'Mudar para viagem',
        'to-dine-in'  => 'Mudar para comer no local',
    ],

    'order-name' => [
        'label'       => 'Editar nome do pedido',
        'heading'     => 'Editar nome do pedido',
        'placeholder' => 'ex.: 18:45 João 4P',
    ],

    'tip' => [
        'label'  => 'Gorjeta',
        'add'    => 'Adicionar gorjeta',
        'change' => 'Alterar gorjeta',
        'remove' => 'Remover gorjeta',
    ],

    'global-discount' => [
        'label'   => 'Desconto',
        'heading' => 'Percentual de desconto',
        'apply'   => 'Aplicar',
        'hint'    => 'Substitui qualquer desconto já aplicado ao pedido.',
    ],

    'booking' => [
        'book'    => 'Reservar mesa',
        'release' => 'Liberar mesa',
    ],

    'transfer' => [
        'label'        => 'Transferir / Mesclar',
        'prompt'       => 'Selecione uma mesa para transferir :order',
        'has-payments' => 'Este pedido tem pagamentos. Remova-os antes de mesclá-lo em outra mesa.',
    ],

    'table-selector' => [
        'label'       => 'Mesa',
        'heading'     => 'Seletor de mesa',
        'hint'        => 'Digite o número da mesa ou um nome para um pedido sem mesa.',
        'placeholder' => 'Número da mesa ou nome',
        'jump'        => 'Ir',
    ],

    'session-closed' => [
        'heading' => 'Caixa fechado',
        'body'    => 'Este caixa foi fechado em outra janela ou dispositivo. Os pedidos abertos neste dispositivo não foram enviados; reabra o caixa para continuar.',
        'back'    => 'Voltar aos caixas',
    ],

    'floor-plan' => [
        'edit'                 => 'Editar planta',
        'switch-view'          => 'Alternar visualização do andar',
        'view-map'             => 'Visão de mapa',
        'view-grid'            => 'Visão em grade',
        'floor-name'           => 'Nome do andar',
        'background'           => 'Fundo do andar',
        'no-colour'            => 'Sem cor',
        'add-table'            => 'Adicionar mesa',
        'add-floor'            => 'Adicionar andar',
        'delete-floor'         => 'Excluir andar',
        'confirm-delete-floor' => 'Confirmar exclusão',
        'table-number'         => 'Mesa',
        'fewer-seats'          => 'Menos lugares',
        'more-seats'           => 'Mais lugares',
        'make-square'          => 'Tornar quadrada',
        'make-round'           => 'Tornar redonda',
        'duplicate'            => 'Duplicar',
        'delete-table'         => 'Excluir mesa',
        'hint'                 => 'Arraste as mesas para movê-las, arraste o canto para redimensionar e toque em uma mesa para editá-la.',
        'new-floor'            => 'Andar :number',
        'failed'               => 'Não foi possível salvar a planta (:status).',
        'add-image'            => 'Adicionar imagem',
        'change-image'         => 'Trocar imagem',
        'remove-image'         => 'Remover imagem',
        'zoom-in'              => 'Aproximar',
        'zoom-out'             => 'Afastar',
        'fit'                  => 'Ajustar à tela',
        'unreachable'          => 'Não foi possível conectar ao servidor. Verifique a conexão e tente novamente.',

        'colours' => [
            'red'        => 'Vermelho',
            'orange'     => 'Laranja',
            'yellow'     => 'Amarelo',
            'green'      => 'Verde',
            'teal'       => 'Verde-azulado',
            'blue'       => 'Azul',
            'violet'     => 'Violeta',
            'pink'       => 'Rosa',
            'stone'      => 'Pedra',
            'white'      => 'Branco',
            'light-grey' => 'Cinza-claro',
            'cream'      => 'Creme',
            'mint'       => 'Menta',
            'sky'        => 'Céu',
            'lavender'   => 'Lavanda',
            'blush'      => 'Rosa-claro',
            'warm-grey'  => 'Cinza quente',
        ],
    ],

    'receipt' => [
        'phone'     => 'Tel.:',
        'served-by' => 'Atendido por :cashier',
        'untaxed'   => 'Valor sem impostos',
        'rounding'  => 'Arredondamento',
        'total'     => 'TOTAL',
        'change'    => 'Troco',
        'order'     => 'Pedido :order',
        'new-order' => 'Novo pedido',
        'guests'    => '{1} :count cliente|[2,*] :count clientes',
    ],

    'install' => [
        'installed'   => 'Este caixa já está instalado como aplicativo neste dispositivo.',
        'unavailable' => 'Este navegador não consegue instalar o caixa. É necessário Chrome ou Edge com HTTPS.',
        'label'       => 'Instalar o app',
    ],

    'scanner' => [
        'unsupported'   => 'Este navegador não consegue escanear com a câmera. Use um leitor de código de barras conectado.',
        'hardware-hint' => 'Um leitor de código de barras conectado funciona em todo o PDV sem abrir isto.',
        'heading'       => 'Escanear um código de barras',
        'start'         => 'Escanear com a câmera',
        'stop'          => 'Parar',
    ],

    'drafts' => [
        'not-shared' => 'Este pedido não está compartilhado com os outros caixas',
        'rejected'   => 'O servidor recusou este pedido.',
    ],

    'kitchen' => [
        'order'        => 'Pedido',
        'new'          => 'Novo',
        'cancelled'    => 'Cancelado',
        'note-changed' => 'Observação alterada',
        'dine-in'      => 'Comer aqui',
        'takeaway'     => 'Para viagem',
        'to-takeaway'  => 'Comer aqui → Para viagem',
        'to-dine-in'   => 'Para viagem → Comer aqui',
        'by'           => 'Por :name',
        'table'        => 'Mesa :table',
        'order-note'   => 'Observação do pedido',
        'offline'      => 'Enviar para a cozinha requer conexão.',
        'nothing'      => 'Nada novo para enviar à cozinha.',
    ],

    'rejected' => [
        'settled-heading' => 'Pago em outro caixa',
        'failed-heading'  => 'Este pedido não foi salvo',
        'order'           => 'Pedido :order',
        'table'           => 'Mesa :table',
        'settled-body'    => ':order já foi pago em outro caixa. Esta venda não foi registrada.',
        'give-back'       => 'Devolva :amount ao cliente.',
        'failed-body'     => 'Não foi possível salvar :order no servidor.',
        'returned'        => 'Dinheiro devolvido',
        'dismiss'         => 'Dispensar',
    ],

    'offline' => [
        'banner'              => 'Offline — as vendas continuam e serão sincronizadas quando a conexão voltar',
        'waiting'             => ':count pedido(s) aguardando sincronização',
        'rejected'            => ':count pedido(s) rejeitados pelo servidor',
        'storage-unavailable' => 'Armazenamento local indisponível — recarregue antes de registrar mais pedidos',
        'retry'               => 'Tentar novamente',
        'lost-heading'        => 'Conexão perdida',
        'lost-body'           => 'Até que a conexão seja restabelecida, o ponto de venda funcionará com funcionalidade limitada.',
        'continue'            => 'Continuar com funcionalidade limitada',
    ],

    'actions' => [
        'customer' => 'Cliente',
        'note'     => 'Observação',
        'payment'  => 'Pagamento',
        'heading'  => 'Ações',
        'label'    => 'Ações',

        'cancel-order' => [
            'label'   => 'Cancelar pedido',
            'confirm' => 'Confirmar',
            'hint'    => 'As linhas dele serão perdidas. Esta ação não pode ser desfeita.',
            'failed'  => 'Não foi possível cancelar o pedido (:status).',
            'offline' => 'Cancelar este pedido requer conexão.',
        ],
    ],

    'price-lists' => [
        'label'   => 'Lista de preços',
        'heading' => 'Selecione a lista de preços',
        'default' => 'Preço padrão',
    ],

    'notification' => [
        'success' => [
            'title' => 'Pedido concluído',
            'body'  => 'O pedido :order foi liquidado.',
        ],

        'queued' => [
            'title' => 'Pedido enfileirado offline',
            'body'  => ':count pedido(s) serão sincronizados quando a conexão voltar.',
        ],
    ],
];
