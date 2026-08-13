<?php

return [
    'navigation' => [
        'title' => 'Champs personnalisés',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Nom',
                    'code'              => 'code',
                    'code-helper-text'  => 'Le code doit commencer par une lettre ou un tiret bas, et ne peut contenir que des lettres, des chiffres et des tirets bas.',
                ],
            ],

            'options' => [
                'title' => 'Options',

                'fields' => [
                    'add-option' => 'Ajouter une option',
                ],
            ],

            'form-settings' => [
                'title' => 'Paramètres du formulaire',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validations',

                        'fields' => [
                            'validation'     => 'Validation',
                            'field'          => 'Champ',
                            'value'          => 'Valeur',
                            'add-validation' => 'Ajouter une validation',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Paramètres supplémentaires',

                        'fields' => [
                            'setting'     => 'Paramètre',
                            'value'       => 'Valeur',
                            'color'       => 'Couleur',
                            'add-setting' => 'Ajouter un paramètre',

                            'color-options' => [
                                'danger'    => 'Danger',
                                'info'      => 'Info',
                                'primary'   => 'Primaire',
                                'secondary' => 'Secondaire',
                                'warning'   => 'Avertissement',
                                'success'   => 'Succès',
                            ],

                            'grid-options' => [
                                'row'    => 'Ligne',
                                'column' => 'Colonne',
                            ],

                            'input-modes' => [
                                'text'     => 'Texte',
                                'email'    => 'E-mail',
                                'numeric'  => 'Numérique',
                                'integer'  => 'Entier',
                                'password' => 'Mot de passe',
                                'tel'      => 'Téléphone',
                                'url'      => 'URL',
                                'color'    => 'Couleur',
                                'none'     => 'Aucun',
                                'decimal'  => 'Décimal',
                                'search'   => 'Recherche',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Supérieur à',
                        'gte'                  => 'Supérieur ou égal à',
                        'lt'                   => 'Inférieur à',
                        'lte'                  => 'Inférieur ou égal à',
                        'max-size'             => 'Taille maximale',
                        'min-size'             => 'Taille minimale',
                        'multiple-of'          => 'Multiple de',
                        'nullable'             => 'Nullable',
                        'prohibited'           => 'Interdit',
                        'prohibited-if'        => 'Interdit si',
                        'prohibited-unless'    => 'Interdit sauf si',
                        'prohibits'            => 'Interdit',
                        'required'             => 'Requis',
                        'required-if'          => 'Requis si',
                        'required-if-accepted' => 'Requis si accepté',
                        'required-unless'      => 'Requis sauf si',
                        'required-with'        => 'Requis avec',
                        'required-with-all'    => 'Requis avec tous',
                        'required-without'     => 'Requis sans',
                        'required-without-all' => 'Requis sans tous',
                        'rules'                => 'Règles personnalisées',
                        'unique'               => 'Unique',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alpha tiret',
                        'alpha-num'         => 'Alphanumérique',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'Ne se termine pas par',
                        'doesnt-start-with' => 'Ne commence pas par',
                        'ends-with'         => 'Se termine par',
                        'filled'            => 'Rempli',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Longueur',
                        'mac-address'       => 'Adresse MAC',
                        'max-length'        => 'Longueur maximale',
                        'min-length'        => 'Longueur minimale',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Commence par',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Rempli',
                        'max-length' => 'Longueur maximale',
                        'min-length' => 'Longueur minimale',
                    ],

                    'select' => [
                        'different'  => 'Différent',
                        'exists'     => 'Existe',
                        'in'         => 'Dans',
                        'not-in'     => 'Pas dans',
                        'same'       => 'Identique',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Accepté',
                        'declined' => 'Refusé',
                    ],

                    'toggle' => [
                        'accepted' => 'Accepté',
                        'declined' => 'Refusé',
                    ],

                    'checkbox-list' => [
                        'in'        => 'Dans',
                        'max-items' => 'Nombre maximum d\'éléments',
                        'min-items' => 'Nombre minimum d\'éléments',
                    ],

                    'datetime' => [
                        'after'           => 'Après',
                        'after-or-equal'  => 'Après ou égal à',
                        'before'          => 'Avant',
                        'before-or-equal' => 'Avant ou égal à',
                    ],

                    'editor' => [
                        'filled'     => 'Rempli',
                        'max-length' => 'Longueur maximale',
                        'min-length' => 'Longueur minimale',
                    ],

                    'markdown' => [
                        'filled'     => 'Rempli',
                        'max-length' => 'Longueur maximale',
                        'min-length' => 'Longueur minimale',
                    ],

                    'color' => [
                        'hex-color' => 'Couleur hexadécimale',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Mise en majuscule automatique',
                        'autocomplete'      => 'Saisie semi-automatique',
                        'autofocus'         => 'Focus automatique',
                        'default'           => 'Valeur par défaut',
                        'disabled'          => 'Désactivé',
                        'helper-text'       => "Texte d'aide",
                        'hint'              => 'Indice',
                        'hint-color'        => "Couleur de l'indice",
                        'hint-icon'         => "Icône de l'indice",
                        'id'                => 'Id',
                        'input-mode'        => 'Mode de saisie',
                        'mask'              => 'Masque',
                        'placeholder'       => 'Texte indicatif',
                        'prefix'            => 'Préfixe',
                        'prefix-icon'       => 'Icône de préfixe',
                        'prefix-icon-color' => 'Couleur de l\'icône de préfixe',
                        'read-only'         => 'Lecture seule',
                        'step'              => 'Pas',
                        'suffix'            => 'Suffixe',
                        'suffix-icon'       => 'Icône de suffixe',
                        'suffix-icon-color' => "Couleur de l'icône de suffixe",
                    ],

                    'textarea' => [
                        'autofocus'    => 'Focus automatique',
                        'autosize'     => 'Taille automatique',
                        'cols'         => 'Colonnes',
                        'default'      => 'Valeur par défaut',
                        'disabled'     => 'Désactivé',
                        'helperText'   => "Texte d'aide",
                        'hint'         => 'Indice',
                        'hintColor'    => "Couleur de l'indice",
                        'hintIcon'     => "Icône de l'indice",
                        'id'           => 'Id',
                        'placeholder'  => 'Texte indicatif',
                        'read-only'    => 'Lecture seule',
                        'rows'         => 'Lignes',
                    ],

                    'select' => [
                        'default'                   => 'Valeur par défaut',
                        'disabled'                  => 'Désactivé',
                        'helper-text'               => "Texte d'aide",
                        'hint'                      => 'Indice',
                        'hint-color'                => "Couleur de l'indice",
                        'hint-icon'                 => "Icône de l'indice",
                        'id'                        => 'Id',
                        'loading-message'           => 'Message de chargement',
                        'no-search-results-message' => 'Message aucun résultat de recherche',
                        'options-limit'             => "Limite d'options",
                        'preload'                   => 'Préchargement',
                        'searchable'                => 'Recherchable',
                        'search-debounce'           => 'Délai de recherche',
                        'searching-message'         => 'Message de recherche en cours',
                        'search-prompt'             => 'Invite de recherche',
                    ],

                    'radio' => [
                        'default'     => 'Valeur par défaut',
                        'disabled'    => 'Désactivé',
                        'helper-text' => "Texte d'aide",
                        'hint'        => 'Indice',
                        'hint-color'  => "Couleur de l'indice",
                        'hint-icon'   => "Icône de l'indice",
                        'id'          => 'Id',
                    ],

                    'checkbox' => [
                        'default'     => 'Valeur par défaut',
                        'disabled'    => 'Désactivé',
                        'helper-text' => "Texte d'aide",
                        'hint'        => 'Indice',
                        'hint-color'  => "Couleur de l'indice",
                        'hint-icon'   => "Icône de l'indice",
                        'id'          => 'Id',
                        'inline'      => 'En ligne',
                    ],

                    'toggle' => [
                        'default'     => 'Valeur par défaut',
                        'disabled'    => 'Désactivé',
                        'helper-text' => "Texte d'aide",
                        'hint'        => 'Indice',
                        'hint-color'  => "Couleur de l'indice",
                        'hint-icon'   => "Icône de l'indice",
                        'id'          => 'Id',
                        'off-color'   => 'Couleur désactivée',
                        'off-icon'    => 'Icône désactivée',
                        'on-color'    => 'Couleur activée',
                        'on-icon'     => 'Icône activée',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Bascule groupée',
                        'columns'                   => 'Colonnes',
                        'default'                   => 'Valeur par défaut',
                        'disabled'                  => 'Désactivé',
                        'grid-direction'            => 'Direction de la grille',
                        'helper-text'               => "Texte d'aide",
                        'hint'                      => 'Indice',
                        'hint-color'                => "Couleur de l'indice",
                        'hint-icon'                 => "Icône de l'indice",
                        'id'                        => 'Id',
                        'max-items'                 => "Nombre maximum d'éléments",
                        'min-items'                 => "Nombre minimum d'éléments",
                        'no-search-results-message' => 'Message aucun résultat de recherche',
                        'searchable'                => 'Recherchable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Fermer à la sélection de la date',
                        'default'                 => 'Valeur par défaut',
                        'disabled'                => 'Désactivé',
                        'disabled-dates'          => 'Dates désactivées',
                        'display-format'          => "Format d'affichage",
                        'first-fay-of-week'       => 'Premier jour de la semaine',
                        'format'                  => 'Format',
                        'helper-text'             => "Texte d'aide",
                        'hint'                    => 'Indice',
                        'hint-color'              => "Couleur de l'indice",
                        'hint-icon'               => "Icône de l'indice",
                        'hours-step'              => 'Pas des heures',
                        'id'                      => 'Id',
                        'locale'                  => 'Locale',
                        'minutes-step'            => 'Pas des minutes',
                        'seconds'                 => 'Secondes',
                        'seconds-step'            => 'Pas des secondes',
                        'timezone'                => 'Fuseau horaire',
                        'week-starts-on-monday'   => 'La semaine commence le lundi',
                        'week-starts-on-sunday'   => 'La semaine commence le dimanche',
                    ],

                    'editor' => [
                        'default'      => 'Valeur par défaut',
                        'disabled'     => 'Désactivé',
                        'helper-text'  => "Texte d'aide",
                        'hint'         => 'Indice',
                        'hint-color'   => "Couleur de l'indice",
                        'hint-icon'    => "Icône de l'indice",
                        'id'           => 'Id',
                        'placeholder'  => 'Texte indicatif',
                        'read-only'    => 'Lecture seule',
                    ],

                    'markdown' => [
                        'default'      => 'Valeur par défaut',
                        'disabled'     => 'Désactivé',
                        'helper-text'  => "Texte d'aide",
                        'hint'         => 'Indice',
                        'hint-color'   => "Couleur de l'indice",
                        'hint-icon'    => "Icône de l'indice",
                        'id'           => 'Id',
                        'placeholder'  => 'Texte indicatif',
                        'read-only'    => 'Lecture seule',
                    ],

                    'color' => [
                        'default'     => 'Valeur par défaut',
                        'disabled'    => 'Désactivé',
                        'helper-text' => "Texte d'aide",
                        'hint'        => 'Indice',
                        'hint-color'  => "Couleur de l'indice",
                        'hint-icon'   => "Icône de l'indice",
                        'hsl'         => 'HSL',
                        'id'          => 'Id',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Types de fichiers acceptés',
                        'append-files'                          => 'Ajouter des fichiers',
                        'deletable'                             => 'Supprimable',
                        'directory'                             => 'Répertoire',
                        'downloadable'                          => 'Téléchargeable',
                        'fetch-file-information'                => 'Récupérer les informations du fichier',
                        'file-attachments-directory'            => 'Répertoire des pièces jointes',
                        'file-attachments-visibility'           => 'Visibilité des pièces jointes',
                        'image'                                 => 'Image',
                        'image-crop-aspect-ratio'               => "Rapport d'aspect du recadrage d'image",
                        'image-editor'                          => "Éditeur d'image",
                        'image-editor-aspect-ratios'            => "Rapports d'aspect de l'éditeur d'image",
                        'image-editor-empty-fill-color'         => "Couleur de remplissage vide de l'éditeur d'image",
                        'image-editor-mode'                     => "Mode de l'éditeur d'image",
                        'image-preview-height'                  => "Hauteur de l'aperçu de l'image",
                        'image-resize-mode'                     => "Mode de redimensionnement de l'image",
                        'image-resize-target-height'            => "Hauteur cible du redimensionnement de l'image",
                        'image-resize-target-width'             => "Largeur cible du redimensionnement de l'image",
                        'loading-indicator-position'            => "Position de l'indicateur de chargement",
                        'move-files'                            => 'Déplacer les fichiers',
                        'openable'                              => 'Ouvrable',
                        'orient-images-from-exif'                => "Orienter les images à partir de l'EXIF",
                        'panel-aspect-ratio'                    => "Rapport d'aspect du panneau",
                        'panel-layout'                          => 'Disposition du panneau',
                        'previewable'                           => 'Prévisualisable',
                        'remove-uploaded-file-button-position'  => 'Position du bouton de suppression du fichier téléversé',
                        'reorderable'                           => 'Réorganisable',
                        'store-files'                           => 'Stocker les fichiers',
                        'upload-button-position'                => 'Position du bouton de téléversement',
                        'uploading-message'                     => 'Message de téléversement en cours',
                        'upload-progress-indicator-position'    => "Position de l'indicateur de progression du téléversement",
                        'visibility'                            => 'Visibilité',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Paramètres du tableau',

                'fields' => [
                    'use-in-table'  => 'Utiliser dans le tableau',
                    'setting'       => 'Paramètre',
                    'value'         => 'Valeur',
                    'color'         => 'Couleur',
                    'alignment'     => 'Alignement',
                    'font-weight'   => 'Graisse de police',
                    'icon-position' => "Position de l'icône",
                    'size'          => 'Taille',
                    'add-setting'   => 'Ajouter un paramètre',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primaire',
                        'secondary' => 'Secondaire',
                        'warning'   => 'Avertissement',
                        'success'   => 'Succès',
                    ],

                    'alignment-options' => [
                        'start'   => 'Début',
                        'left'    => 'Gauche',
                        'center'  => 'Centre',
                        'end'     => 'Fin',
                        'right'   => 'Droite',
                        'justify' => 'Justifié',
                        'between' => 'Espacé',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Fin',
                        'extra-light' => 'Extra léger',
                        'light'       => 'Léger',
                        'normal'      => 'Normal',
                        'medium'      => 'Moyen',
                        'semi-bold'   => 'Semi-gras',
                        'bold'        => 'Gras',
                        'extra-bold'  => 'Extra gras',
                        'black'       => 'Noir',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Avant',
                        'after'   => 'Après',
                    ],

                    'size-options' => [
                        'extra-small' => 'Très petit',
                        'small'       => 'Petit',
                        'medium'      => 'Moyen',
                        'large'       => 'Grand',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Aligner à la fin',
                        'alignment'              => 'Alignement',
                        'align-start'            => 'Aligner au début',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Booléen',
                        'color'                  => 'Couleur',
                        'copyable'               => 'Copiable',
                        'copy-message'           => 'Message de copie',
                        'copy-message-duration'  => 'Durée du message de copie',
                        'default'                => 'Par défaut',
                        'filterable'             => 'Filtrable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Agrandir',
                        'icon'                   => 'Icône',
                        'icon-color'             => "Couleur de l'icône",
                        'icon-position'          => "Position de l'icône",
                        'label'                  => 'Libellé',
                        'limit'                  => 'Limite',
                        'line-clamp'             => 'Nombre de lignes maximum',
                        'money'                  => 'Monétaire',
                        'placeholder'            => 'Texte indicatif',
                        'prefix'                 => 'Préfixe',
                        'searchable'             => 'Recherchable',
                        'size'                   => 'Taille',
                        'sortable'               => 'Triable',
                        'suffix'                 => 'Suffixe',
                        'toggleable'             => 'Basculable',
                        'tooltip'                => 'Infobulle',
                        'vertical-alignment'     => 'Alignement vertical',
                        'vertically-align-start' => 'Aligner verticalement au début',
                        'weight'                 => 'Graisse',
                        'width'                  => 'Largeur',
                        'words'                  => 'Mots',
                        'wrap-header'            => "Retour à la ligne de l'en-tête",
                        'column-span'            => 'Étendue de la colonne',
                        'helper-text'            => "Texte d'aide",
                        'hint'                   => 'Indice',
                        'hint-color'             => "Couleur de l'indice",
                        'hint-icon'              => "Icône de l'indice",
                    ],

                    'datetime' => [
                        'date'              => 'Date',
                        'date-time'         => 'Date et heure',
                        'date-time-tooltip' => 'Infobulle de date et heure',
                        'since'             => 'Depuis',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => "Paramètres de la fiche d'information",

                'fields' => [
                    'setting'       => 'Paramètre',
                    'value'         => 'Valeur',
                    'color'         => 'Couleur',
                    'font-weight'   => 'Graisse de police',
                    'icon-position' => "Position de l'icône",
                    'size'          => 'Taille',
                    'add-setting'   => 'Ajouter un paramètre',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primaire',
                        'secondary' => 'Secondaire',
                        'warning'   => 'Avertissement',
                        'success'   => 'Succès',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Fin',
                        'extra-light' => 'Extra léger',
                        'light'       => 'Léger',
                        'normal'      => 'Normal',
                        'medium'      => 'Moyen',
                        'semi-bold'   => 'Semi-gras',
                        'bold'        => 'Gras',
                        'extra-bold'  => 'Extra gras',
                        'black'       => 'Noir',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Avant',
                        'after'   => 'Après',
                    ],

                    'size-options' => [
                        'extra-small' => 'Très petit',
                        'small'       => 'Petit',
                        'medium'      => 'Moyen',
                        'large'       => 'Grand',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Aligner à la fin',
                        'alignment'              => 'Alignement',
                        'align-start'            => 'Aligner au début',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Booléen',
                        'color'                  => 'Couleur',
                        'copyable'               => 'Copiable',
                        'copy-message'           => 'Message de copie',
                        'copy-message-duration'  => 'Durée du message de copie',
                        'default'                => 'Par défaut',
                        'filterable'             => 'Filtrable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Agrandir',
                        'icon'                   => 'Icône',
                        'icon-color'             => "Couleur de l'icône",
                        'icon-position'          => "Position de l'icône",
                        'label'                  => 'Libellé',
                        'limit'                  => 'Limite',
                        'line-clamp'             => 'Nombre de lignes maximum',
                        'money'                  => 'Monétaire',
                        'placeholder'            => 'Texte indicatif',
                        'prefix'                 => 'Préfixe',
                        'searchable'             => 'Recherchable',
                        'size'                   => 'Taille',
                        'sortable'               => 'Triable',
                        'suffix'                 => 'Suffixe',
                        'toggleable'             => 'Basculable',
                        'tooltip'                => 'Infobulle',
                        'vertical-alignment'     => 'Alignement vertical',
                        'vertically-align-start' => 'Aligner verticalement au début',
                        'weight'                 => 'Graisse',
                        'width'                  => 'Largeur',
                        'words'                  => 'Mots',
                        'wrap-header'            => "Retour à la ligne de l'en-tête",
                        'column-span'            => 'Étendue de la colonne',
                        'helper-text'            => "Texte d'aide",
                        'hint'                   => 'Indice',
                        'hint-color'             => "Couleur de l'indice",
                        'hint-icon'              => "Icône de l'indice",
                    ],

                    'datetime' => [
                        'date'              => 'Date',
                        'date-time'         => 'Date et heure',
                        'date-time-tooltip' => 'Infobulle de date et heure',
                        'since'             => 'Depuis',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Séparateur',
                        'list-with-line-breaks'    => 'Liste avec sauts de ligne',
                        'bulleted'                 => 'À puces',
                        'limit-list'               => 'Limiter la liste',
                        'expandable-limited-list'  => 'Liste limitée extensible',
                    ],

                    'select' => [
                        'separator'                => 'Séparateur',
                        'list-with-line-breaks'    => 'Liste avec sauts de ligne',
                        'bulleted'                 => 'À puces',
                        'limit-list'               => 'Limiter la liste',
                        'expandable-limited-list'  => 'Liste limitée extensible',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Booléen',
                        'false-icon'  => 'Icône faux',
                        'true-icon'   => 'Icône vrai',
                        'true-color'  => 'Couleur vrai',
                        'false-color' => 'Couleur faux',
                    ],

                    'toggle' => [
                        'boolean'     => 'Booléen',
                        'false-icon'  => 'Icône faux',
                        'true-icon'   => 'Icône vrai',
                        'true-color'  => 'Couleur vrai',
                        'false-color' => 'Couleur faux',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'fields' => [
                    'type'           => 'Type',
                    'input-type'     => 'Type de saisie',
                    'is-multiselect' => 'Est multisélection',
                    'sort-order'     => 'Ordre de tri',

                    'type-options' => [
                        'text'          => 'Saisie de texte',
                        'textarea'      => 'Zone de texte',
                        'select'        => 'Liste déroulante',
                        'checkbox'      => 'Case à cocher',
                        'radio'         => 'Bouton radio',
                        'toggle'        => 'Interrupteur',
                        'checkbox-list' => 'Liste de cases à cocher',
                        'datetime'      => 'Sélecteur de date et heure',
                        'editor'        => 'Éditeur de texte enrichi',
                        'markdown'      => 'Éditeur Markdown',
                        'color'         => 'Sélecteur de couleur',
                    ],

                    'input-type-options' => [
                        'text'     => 'Texte',
                        'email'    => 'E-mail',
                        'numeric'  => 'Numérique',
                        'integer'  => 'Entier',
                        'password' => 'Mot de passe',
                        'tel'      => 'Téléphone',
                        'url'      => 'URL',
                        'color'    => 'Couleur',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Ressource',

                'fields' => [
                    'plugin'   => 'Plugin',
                    'resource' => 'Ressource',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Code',
            'name'       => 'Nom',
            'type'       => 'Type',
            'resource'   => 'Ressource',
            'created-at' => 'Créé le',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Type',
            'resource' => 'Ressource',

            'type-options' => [
                'text'          => 'Saisie de texte',
                'textarea'      => 'Zone de texte',
                'select'        => 'Liste déroulante',
                'checkbox'      => 'Case à cocher',
                'radio'         => 'Bouton radio',
                'toggle'        => 'Interrupteur',
                'checkbox-list' => 'Liste de cases à cocher',
                'datetime'      => 'Sélecteur de date et heure',
                'editor'        => 'Éditeur de texte enrichi',
                'markdown'      => 'Éditeur Markdown',
                'color'         => 'Sélecteur de couleur',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Champ restauré',
                    'body'  => 'Le champ a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Champ supprimé',
                    'body'  => 'Le champ a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Champ définitivement supprimé',
                    'body'  => 'Le champ a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Champs restaurés',
                    'body'  => 'Les champs ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Champs supprimés',
                    'body'  => 'Les champs ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Champs définitivement supprimés',
                    'body'  => 'Les champs ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],
];
