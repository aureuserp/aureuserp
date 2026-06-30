<?php

return [
    'notification' => [
        'title' => 'Candidato creado',
        'body'  => 'El candidato se ha creado correctamente.',
    ],

    'tabs' => [
        'my-applicants'          => 'Mis candidatos',
        'un-assigned'            => 'Sin asignar',
        'in-progress'            => 'En curso',
        'hired'                  => 'Contratados',
        'refused'                => 'Rechazados',
        'archived'               => 'Archivados',
        'blocked'                => 'Bloqueados',
        'directly-available'     => 'Disponibles de inmediato',
        'created-recently'       => 'Creados recientemente',
        'stage-updated-recently' => 'Etapa actualizada recientemente',
    ],

    'header-actions' => [
        'create-applicant' => [
            'label' => 'Nuevo candidato',

            'modal-title' => 'Crear candidato',

            'form' => [
                'fields' => [
                    'candidate' => 'Candidato',
                ],
            ],
        ],
    ],
];
