<?php

return [
    'layouts' => [
        'list'   => 'Liste',
        'kanban' => 'Kanban',
        'graph'  => 'Graphique',
    ],

    'switcher' => [
        'label' => 'Disposition',
    ],

    'card' => [
        'title'        => 'Titre',
        'priority'     => 'Priorité',
        'quick-create' => 'Ajouter une tâche',
        'assignees'    => 'Assignés',
        'schedule'     => 'Planifier',
        'view'         => 'Voir',
        'edit'         => 'Modifier',
        'delete'       => 'Supprimer',
        'share'        => 'Copier le lien',
        'shared'       => 'Lien copié',
        'tags-more'    => ':count de plus',
    ],

    'column' => [
        'add'               => 'Ajouter une étape',
        'add-project'       => 'Ajouter un projet',
        'edit-project'      => 'Modifier le projet',
        'delete-project'    => 'Supprimer le projet',
        'project-settings'  => 'Paramètres du projet',
        'private'           => 'Privé',
        'none'              => 'Aucun',
        'name'              => 'Nom',
        'edit'              => 'Modifier l\'étape',
        'delete'            => 'Supprimer l\'étape',
        'settings'          => 'Paramètres de l\'étape',
        'empty-heading'     => 'Aucune tâche',
        'empty-description' => 'Glissez une tâche ici ou utilisez + pour en créer une.',
    ],

    'group' => [
        'project'   => 'Projet',
        'stage'     => 'Étape',
        'partner'   => 'Client',
        'company'   => 'Société',
        'milestone' => 'Jalon',
        'state'     => 'Statut',
        'priority'  => 'Priorité',
    ],

    'priority' => [
        'high'   => 'Prioritaire',
        'normal' => 'Normale',
    ],

    'empty' => [
        'heading'     => 'Aucune étape',
        'description' => 'Créez une étape pour commencer à organiser les tâches sur le tableau.',
    ],

    'progress' => [
        'label'   => 'Répartition des statuts d\'activité',
        'overdue' => 'En retard',
        'today'   => 'Aujourd\'hui',
        'planned' => 'Planifié',
        'none'    => 'Aucune activité',
    ],

    'schedule' => [
        'heading'  => 'Planifier la tâche',
        'deadline' => 'Échéance',
    ],

    'activities' => [
        'heading'     => 'Activités planifiées',
        'schedule'    => 'Planifier une activité',
        'deadline'    => "Date d'échéance",
        'assigned-to' => 'Assigné à',
    ],

    'assign' => [
        'heading'      => 'Assigner la tâche',
        'label'        => 'Personnes assignées',
        'save'         => 'Assigner',
        'view-profile' => 'Voir le profil',
    ],

    'graph' => [
        'types' => [
            'bar'  => 'Graphique à barres',
            'line' => 'Graphique en courbes',
            'pie'  => 'Graphique circulaire',
        ],
        'stacked'   => 'Empiler par état',
        'sort-asc'  => 'Trier par ordre croissant',
        'sort-desc' => 'Trier par ordre décroissant',
    ],

    'notifications' => [
        'moved'             => 'Tâche déplacée',
        'state-updated'     => 'État mis à jour',
        'assignees-updated' => 'Personnes assignées mises à jour',
        'task-created'      => 'Tâche créée',
        'task-deleted'      => 'Tâche supprimée',
        'task-scheduled'    => 'Tâche planifiée',
        'project-created'   => 'Projet créé',
        'project-updated'   => 'Projet mis à jour',
        'project-deleted'   => 'Projet supprimé',
        'project-in-use'    => 'Ce projet contient encore des tâches et ne peut pas être supprimé',
        'stage-created'     => 'Étape créée',
        'stage-updated'     => 'Étape mise à jour',
        'stage-in-use'      => 'Cette étape contient encore des tâches et ne peut pas être supprimée',
        'stage-deleted'     => 'Étape supprimée',
    ],
];
