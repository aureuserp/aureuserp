<?php

return [
    'title' => 'Skills',

    'navigation' => [
        'title' => 'Skills',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Details',

                'fields' => [
                    'employee'       => 'Employee',
                    'skill'          => 'Skill',
                    'skill-level'    => 'Level',
                    'skill-type'     => 'Skill Type',
                ],
            ],
            'addition-information' => [
                'title' => 'Additional Information',

                'fields' => [
                    'created-by' => 'Created By',
                    'updated-by' => 'Updated By',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Employee',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Type',
            'user'            => 'User',
            'proficiency'     => 'Proficiency',
            'created-by'      => 'Created By',
            'created-at'      => 'Created At',
        ],

        'filters' => [
            'employee'        => 'Employee',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Type',
            'user'            => 'User',
            'created-by'      => 'Created By',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
        ],

        'groups' => [
            'employee'   => 'Employee',
            'skill-type' => 'Skill Type',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Details',

                'entries' => [
                    'employee'        => 'Employee',
                    'skill'           => 'Skill',
                    'skill-level'     => 'Level',
                    'skill-type'      => 'Skill Type',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'entries' => [
                    'created-by' => 'Created By',
                    'updated-by' => 'Updated By',
                ],
            ],
        ],
    ],
];
