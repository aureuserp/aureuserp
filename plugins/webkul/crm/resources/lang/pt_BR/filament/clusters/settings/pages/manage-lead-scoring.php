<?php

return [
    'title'       => 'Gerenciar pontuação de leads',
    'description' => 'A probabilidade de ganhar cada lead e oportunidade aberta é calculada a partir do histórico de ganhos e perdas. A etapa e a equipe de vendas são sempre usadas.',

    'form' => [
        'variables'          => 'Considerar também',
        'variables-helper'   => 'Quais dados de um registro a probabilidade considera.',
        'start-date'         => 'Considerar leads criados a partir de',
        'start-date-helper'  => 'Deixe vazio para usar todo o histórico.',
        'update'             => 'Atualizar probabilidades de pontuação',
        'update-description' => 'Salva estas escolhas, reaprende com o histórico de ganhos e perdas e recalcula todos os registros abertos.',
        'updated'            => 'Probabilidades atualizadas para :count registro(s) aberto(s).',
    ],
];
