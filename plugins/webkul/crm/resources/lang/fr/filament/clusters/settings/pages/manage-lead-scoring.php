<?php

return [
    'title'       => 'Gérer la notation des pistes',
    'description' => 'La probabilité de gagner chaque piste et opportunité ouverte est calculée à partir de l\'historique des gains et des pertes. L\'étape et l\'équipe commerciale sont toujours utilisées.',

    'form' => [
        'variables'          => 'Prendre aussi en compte',
        'variables-helper'   => 'Les informations d\'un enregistrement prises en compte par la probabilité.',
        'start-date'         => 'Prendre en compte les pistes créées à partir du',
        'start-date-helper'  => 'Laissez vide pour utiliser tout l\'historique.',
        'update'             => 'Mettre à jour les probabilités de scoring',
        'update-description' => 'Enregistre ces choix, réapprend de l\'historique des gains et des pertes et recalcule tous les enregistrements ouverts.',
        'updated'            => 'Probabilités mises à jour pour :count enregistrement(s) ouvert(s).',
    ],
];
