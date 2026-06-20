<?php

return [

    'uninstall' => [
        'title' => 'Confirmação de desinstalação',
        'message' => 'Tem certeza de que deseja desinstalar o plugin :name?',
        'warning' => '⚠️ Esta ação não pode ser desfeita e excluirá dados permanentemente.',
    ],

    'dependents' => [
        'title' => 'Plugins dependentes',
        'description' => 'Estes plugins dependem deste e também serão desinstalados.',
        'installed' => 'Instalado',
        'not_installed' => 'Não instalado',
    ],

    'data_impact' => [
        'title' => 'Impacto nos dados',
        'description' => 'As tabelas de banco de dados a seguir contêm dados que serão excluídos permanentemente.',
        'records' => ':count registros',
    ],

];
