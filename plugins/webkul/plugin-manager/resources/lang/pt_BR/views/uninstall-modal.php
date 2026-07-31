<?php

return [

    'uninstall' => [
        'title' => 'Confirmação de desinstalação',
        'message' => 'Tem certeza de que deseja desinstalar o plugin :name?',
        'warning' => '⚠️ Esta ação não pode ser desfeita e excluirá dados permanentemente.',
    ],

    'dependents' => [
        'title' => 'Plugins dependentes',
        'description' => 'Estes plugins dependem deste. Os dependentes instalados devem ser desinstalados primeiro.',
        'installed' => 'Instalado',
        'not_installed' => 'Não instalado',
    ],

    'dependency_warning' => [
        'title' => 'Ação necessária',
        'message' => '⚠️ Desinstale primeiro os seguintes plugins dependentes antes de desinstalar :name.',
    ],

    'data_impact' => [
        'title' => 'Impacto nos dados',
        'description' => 'As tabelas de banco de dados a seguir contêm dados que serão excluídos permanentemente.',
        'records' => ':count registros',
    ],

];
