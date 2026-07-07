<?php

return [
    'navigation' => [
        'title' => 'Campos personalizados',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Nombre',
                    'code'              => 'Código',
                    'code-helper-text'  => 'El código debe comenzar con una letra o un guion bajo, y solo puede contener letras, números y guiones bajos.',
                ],
            ],

            'options' => [
                'title' => 'Opciones',

                'fields' => [
                    'add-option' => 'Agregar opción',
                ],
            ],

            'form-settings' => [
                'title' => 'Configuración del formulario',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validaciones',

                        'fields' => [
                            'validation'     => 'Validación',
                            'field'          => 'Campo',
                            'value'          => 'Valor',
                            'add-validation' => 'Agregar validación',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Configuración adicional',

                        'fields' => [
                            'setting'     => 'Ajuste',
                            'value'       => 'Valor',
                            'color'       => 'Color',
                            'add-setting' => 'Agregar ajuste',

                            'color-options' => [
                                'danger'    => 'Peligro',
                                'info'      => 'Información',
                                'primary'   => 'Primario',
                                'secondary' => 'Secundario',
                                'warning'   => 'Advertencia',
                                'success'   => 'Éxito',
                            ],

                            'grid-options' => [
                                'row'    => 'Fila',
                                'column' => 'Columna',
                            ],

                            'input-modes' => [
                                'text'     => 'Texto',
                                'email'    => 'Correo electrónico',
                                'numeric'  => 'Numérico',
                                'integer'  => 'Entero',
                                'password' => 'Contraseña',
                                'tel'      => 'Teléfono',
                                'url'      => 'URL',
                                'color'    => 'Color',
                                'none'     => 'Ninguno',
                                'decimal'  => 'Decimal',
                                'search'   => 'Buscar',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Mayor que',
                        'gte'                  => 'Mayor o igual que',
                        'lt'                   => 'Menor que',
                        'lte'                  => 'Menor o igual que',
                        'max-size'             => 'Tamaño máximo',
                        'min-size'             => 'Tamaño mínimo',
                        'multiple-of'          => 'Múltiplo de',
                        'nullable'             => 'Anulable',
                        'prohibited'           => 'Prohibido',
                        'prohibited-if'        => 'Prohibido si',
                        'prohibited-unless'    => 'Prohibido a menos que',
                        'prohibits'            => 'Prohíbe',
                        'required'             => 'Obligatorio',
                        'required-if'          => 'Obligatorio si',
                        'required-if-accepted' => 'Obligatorio si se acepta',
                        'required-unless'      => 'Obligatorio a menos que',
                        'required-with'        => 'Obligatorio con',
                        'required-with-all'    => 'Obligatorio con todos',
                        'required-without'     => 'Obligatorio sin',
                        'required-without-all' => 'Obligatorio sin todos',
                        'rules'                => 'Reglas personalizadas',
                        'unique'               => 'Único',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alfanumérico con guiones',
                        'alpha-num'         => 'Alfanumérico',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'No termina con',
                        'doesnt-start-with' => 'No comienza con',
                        'ends-with'         => 'Termina con',
                        'filled'            => 'Completado',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Longitud',
                        'mac-address'       => 'Dirección MAC',
                        'max-length'        => 'Longitud máxima',
                        'min-length'        => 'Longitud mínima',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Comienza con',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Completado',
                        'max-length' => 'Longitud máxima',
                        'min-length' => 'Longitud mínima',
                    ],

                    'select' => [
                        'different'  => 'Diferente',
                        'exists'     => 'Existe',
                        'in'         => 'En',
                        'not-in'     => 'No en',
                        'same'       => 'Igual',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Aceptado',
                        'declined' => 'Rechazado',
                    ],

                    'toggle' => [
                        'accepted' => 'Aceptado',
                        'declined' => 'Rechazado',
                    ],

                    'checkbox-list' => [
                        'in'        => 'En',
                        'max-items' => 'Máximo de elementos',
                        'min-items' => 'Mínimo de elementos',
                    ],

                    'datetime' => [
                        'after'           => 'Después de',
                        'after-or-equal'  => 'Después o igual a',
                        'before'          => 'Antes de',
                        'before-or-equal' => 'Antes o igual a',
                    ],

                    'editor' => [
                        'filled'     => 'Completado',
                        'max-length' => 'Longitud máxima',
                        'min-length' => 'Longitud mínima',
                    ],

                    'markdown' => [
                        'filled'     => 'Completado',
                        'max-length' => 'Longitud máxima',
                        'min-length' => 'Longitud mínima',
                    ],

                    'color' => [
                        'hex-color' => 'Color hexadecimal',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Autocapitalización',
                        'autocomplete'      => 'Autocompletar',
                        'autofocus'         => 'Enfoque automático',
                        'default'           => 'Valor predeterminado',
                        'disabled'          => 'Deshabilitado',
                        'helper-text'       => 'Texto de ayuda',
                        'hint'              => 'Sugerencia',
                        'hint-color'        => 'Color de la sugerencia',
                        'hint-icon'         => 'Icono de la sugerencia',
                        'id'                => 'Id',
                        'input-mode'        => 'Modo de entrada',
                        'mask'              => 'Máscara',
                        'placeholder'       => 'Marcador de posición',
                        'prefix'            => 'Prefijo',
                        'prefix-icon'       => 'Icono del prefijo',
                        'prefix-icon-color' => 'Color del icono del prefijo',
                        'read-only'         => 'Solo lectura',
                        'step'              => 'Paso',
                        'suffix'            => 'Sufijo',
                        'suffix-icon'       => 'Icono del sufijo',
                        'suffix-icon-color' => 'Color del icono del sufijo',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Enfoque automático',
                        'autosize'     => 'Tamaño automático',
                        'cols'         => 'Columnas',
                        'default'      => 'Valor predeterminado',
                        'disabled'     => 'Deshabilitado',
                        'helperText'   => 'Texto de ayuda',
                        'hint'         => 'Sugerencia',
                        'hintColor'    => 'Color de la sugerencia',
                        'hintIcon'     => 'Icono de la sugerencia',
                        'id'           => 'Id',
                        'placeholder'  => 'Marcador de posición',
                        'read-only'    => 'Solo lectura',
                        'rows'         => 'Filas',
                    ],

                    'select' => [
                        'default'                   => 'Valor predeterminado',
                        'disabled'                  => 'Deshabilitado',
                        'helper-text'               => 'Texto de ayuda',
                        'hint'                      => 'Sugerencia',
                        'hint-color'                => 'Color de la sugerencia',
                        'hint-icon'                 => 'Icono de la sugerencia',
                        'id'                        => 'Id',
                        'loading-message'           => 'Mensaje de carga',
                        'no-search-results-message' => 'Mensaje de sin resultados de búsqueda',
                        'options-limit'             => 'Límite de opciones',
                        'preload'                   => 'Precargar',
                        'searchable'                => 'Buscable',
                        'search-debounce'           => 'Retardo de búsqueda',
                        'searching-message'         => 'Mensaje de búsqueda en curso',
                        'search-prompt'             => 'Texto de búsqueda',
                    ],

                    'radio' => [
                        'default'     => 'Valor predeterminado',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de la sugerencia',
                        'hint-icon'   => 'Icono de la sugerencia',
                        'id'          => 'Id',
                    ],

                    'checkbox' => [
                        'default'     => 'Valor predeterminado',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de la sugerencia',
                        'hint-icon'   => 'Icono de la sugerencia',
                        'id'          => 'Id',
                        'inline'      => 'En línea',
                    ],

                    'toggle' => [
                        'default'     => 'Valor predeterminado',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de la sugerencia',
                        'hint-icon'   => 'Icono de la sugerencia',
                        'id'          => 'Id',
                        'off-color'   => 'Color desactivado',
                        'off-icon'    => 'Icono desactivado',
                        'on-color'    => 'Color activado',
                        'on-icon'     => 'Icono activado',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Conmutable en bloque',
                        'columns'                   => 'Columnas',
                        'default'                   => 'Valor predeterminado',
                        'disabled'                  => 'Deshabilitado',
                        'grid-direction'            => 'Dirección de la cuadrícula',
                        'helper-text'               => 'Texto de ayuda',
                        'hint'                      => 'Sugerencia',
                        'hint-color'                => 'Color de la sugerencia',
                        'hint-icon'                 => 'Icono de la sugerencia',
                        'id'                        => 'Id',
                        'max-items'                 => 'Máximo de elementos',
                        'min-items'                 => 'Mínimo de elementos',
                        'no-search-results-message' => 'Mensaje de sin resultados de búsqueda',
                        'searchable'                => 'Buscable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Cerrar al seleccionar fecha',
                        'default'                 => 'Valor predeterminado',
                        'disabled'                => 'Deshabilitado',
                        'disabled-dates'          => 'Fechas deshabilitadas',
                        'display-format'          => 'Formato de visualización',
                        'first-fay-of-week'       => 'Primer día de la semana',
                        'format'                  => 'Formato',
                        'helper-text'             => 'Texto de ayuda',
                        'hint'                    => 'Sugerencia',
                        'hint-color'              => 'Color de la sugerencia',
                        'hint-icon'               => 'Icono de la sugerencia',
                        'hours-step'              => 'Paso de horas',
                        'id'                      => 'Id',
                        'locale'                  => 'Configuración regional',
                        'minutes-step'            => 'Paso de minutos',
                        'seconds'                 => 'Segundos',
                        'seconds-step'            => 'Paso de segundos',
                        'timezone'                => 'Zona horaria',
                        'week-starts-on-monday'   => 'La semana comienza el lunes',
                        'week-starts-on-sunday'   => 'La semana comienza el domingo',
                    ],

                    'editor' => [
                        'default'      => 'Valor predeterminado',
                        'disabled'     => 'Deshabilitado',
                        'helper-text'  => 'Texto de ayuda',
                        'hint'         => 'Sugerencia',
                        'hint-color'   => 'Color de la sugerencia',
                        'hint-icon'    => 'Icono de la sugerencia',
                        'id'           => 'Id',
                        'placeholder'  => 'Marcador de posición',
                        'read-only'    => 'Solo lectura',
                    ],

                    'markdown' => [
                        'default'      => 'Valor predeterminado',
                        'disabled'     => 'Deshabilitado',
                        'helper-text'  => 'Texto de ayuda',
                        'hint'         => 'Sugerencia',
                        'hint-color'   => 'Color de la sugerencia',
                        'hint-icon'    => 'Icono de la sugerencia',
                        'id'           => 'Id',
                        'placeholder'  => 'Marcador de posición',
                        'read-only'    => 'Solo lectura',
                    ],

                    'color' => [
                        'default'     => 'Valor predeterminado',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de la sugerencia',
                        'hint-icon'   => 'Icono de la sugerencia',
                        'hsl'         => 'HSL',
                        'id'          => 'Id',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Tipos de archivo aceptados',
                        'append-files'                          => 'Adjuntar archivos',
                        'deletable'                             => 'Eliminable',
                        'directory'                             => 'Directorio',
                        'downloadable'                          => 'Descargable',
                        'fetch-file-information'                => 'Obtener información del archivo',
                        'file-attachments-directory'            => 'Directorio de archivos adjuntos',
                        'file-attachments-visibility'           => 'Visibilidad de archivos adjuntos',
                        'image'                                 => 'Imagen',
                        'image-crop-aspect-ratio'               => 'Relación de aspecto del recorte de imagen',
                        'image-editor'                          => 'Editor de imágenes',
                        'image-editor-aspect-ratios'            => 'Relaciones de aspecto del editor de imágenes',
                        'image-editor-empty-fill-color'         => 'Color de relleno vacío del editor de imágenes',
                        'image-editor-mode'                     => 'Modo del editor de imágenes',
                        'image-preview-height'                  => 'Altura de la vista previa de la imagen',
                        'image-resize-mode'                     => 'Modo de redimensionamiento de imagen',
                        'image-resize-target-height'            => 'Altura objetivo de redimensionamiento de imagen',
                        'image-resize-target-width'             => 'Ancho objetivo de redimensionamiento de imagen',
                        'loading-indicator-position'            => 'Posición del indicador de carga',
                        'move-files'                            => 'Mover archivos',
                        'openable'                              => 'Abrible',
                        'orient-images-from-exif'               => 'Orientar imágenes desde EXIF',
                        'panel-aspect-ratio'                    => 'Relación de aspecto del panel',
                        'panel-layout'                          => 'Diseño del panel',
                        'previewable'                           => 'Previsualizable',
                        'remove-uploaded-file-button-position'  => 'Posición del botón para eliminar archivo subido',
                        'reorderable'                           => 'Reordenable',
                        'store-files'                           => 'Almacenar archivos',
                        'upload-button-position'                => 'Posición del botón de subida',
                        'uploading-message'                     => 'Mensaje de subida en curso',
                        'upload-progress-indicator-position'    => 'Posición del indicador de progreso de subida',
                        'visibility'                            => 'Visibilidad',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Configuración de la tabla',

                'fields' => [
                    'use-in-table'  => 'Usar en la tabla',
                    'setting'       => 'Ajuste',
                    'value'         => 'Valor',
                    'color'         => 'Color',
                    'alignment'     => 'Alineación',
                    'font-weight'   => 'Grosor de fuente',
                    'icon-position' => 'Posición del icono',
                    'size'          => 'Tamaño',
                    'add-setting'   => 'Agregar ajuste',

                    'color-options' => [
                        'danger'    => 'Peligro',
                        'info'      => 'Información',
                        'primary'   => 'Primario',
                        'secondary' => 'Secundario',
                        'warning'   => 'Advertencia',
                        'success'   => 'Éxito',
                    ],

                    'alignment-options' => [
                        'start'   => 'Inicio',
                        'left'    => 'Izquierda',
                        'center'  => 'Centro',
                        'end'     => 'Fin',
                        'right'   => 'Derecha',
                        'justify' => 'Justificado',
                        'between' => 'Entre',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extrafino',
                        'light'       => 'Fino',
                        'normal'      => 'Normal',
                        'medium'      => 'Medio',
                        'semi-bold'   => 'Seminegrita',
                        'bold'        => 'Negrita',
                        'extra-bold'  => 'Extranegrita',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Antes',
                        'after'   => 'Después',
                    ],

                    'size-options' => [
                        'extra-small' => 'Muy pequeño',
                        'small'       => 'Pequeño',
                        'medium'      => 'Mediano',
                        'large'       => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Alinear al final',
                        'alignment'              => 'Alineación',
                        'align-start'            => 'Alinear al inicio',
                        'badge'                  => 'Insignia',
                        'boolean'                => 'Booleano',
                        'color'                  => 'Color',
                        'copyable'               => 'Copiable',
                        'copy-message'           => 'Mensaje de copia',
                        'copy-message-duration'  => 'Duración del mensaje de copia',
                        'default'                => 'Predeterminado',
                        'filterable'             => 'Filtrable',
                        'groupable'              => 'Agrupable',
                        'grow'                   => 'Expandir',
                        'icon'                   => 'Icono',
                        'icon-color'             => 'Color del icono',
                        'icon-position'          => 'Posición del icono',
                        'label'                  => 'Etiqueta',
                        'limit'                  => 'Límite',
                        'line-clamp'             => 'Límite de líneas',
                        'money'                  => 'Moneda',
                        'placeholder'            => 'Marcador de posición',
                        'prefix'                 => 'Prefijo',
                        'searchable'             => 'Buscable',
                        'size'                   => 'Tamaño',
                        'sortable'               => 'Ordenable',
                        'suffix'                 => 'Sufijo',
                        'toggleable'             => 'Conmutable',
                        'tooltip'                => 'Información sobre herramientas',
                        'vertical-alignment'     => 'Alineación vertical',
                        'vertically-align-start' => 'Alinear verticalmente al inicio',
                        'weight'                 => 'Grosor',
                        'width'                  => 'Ancho',
                        'words'                  => 'Palabras',
                        'wrap-header'            => 'Ajustar encabezado',
                        'column-span'            => 'Extensión de columna',
                        'helper-text'            => 'Texto de ayuda',
                        'hint'                   => 'Sugerencia',
                        'hint-color'             => 'Color de la sugerencia',
                        'hint-icon'              => 'Icono de la sugerencia',
                    ],

                    'datetime' => [
                        'date'              => 'Fecha',
                        'date-time'         => 'Fecha y hora',
                        'date-time-tooltip' => 'Información de fecha y hora',
                        'since'             => 'Desde',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Configuración de la lista de información',

                'fields' => [
                    'setting'       => 'Ajuste',
                    'value'         => 'Valor',
                    'color'         => 'Color',
                    'font-weight'   => 'Grosor de fuente',
                    'icon-position' => 'Posición del icono',
                    'size'          => 'Tamaño',
                    'add-setting'   => 'Agregar ajuste',

                    'color-options' => [
                        'danger'    => 'Peligro',
                        'info'      => 'Información',
                        'primary'   => 'Primario',
                        'secondary' => 'Secundario',
                        'warning'   => 'Advertencia',
                        'success'   => 'Éxito',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extrafino',
                        'light'       => 'Fino',
                        'normal'      => 'Normal',
                        'medium'      => 'Medio',
                        'semi-bold'   => 'Seminegrita',
                        'bold'        => 'Negrita',
                        'extra-bold'  => 'Extranegrita',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Antes',
                        'after'   => 'Después',
                    ],

                    'size-options' => [
                        'extra-small' => 'Muy pequeño',
                        'small'       => 'Pequeño',
                        'medium'      => 'Mediano',
                        'large'       => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Alinear al final',
                        'alignment'              => 'Alineación',
                        'align-start'            => 'Alinear al inicio',
                        'badge'                  => 'Insignia',
                        'boolean'                => 'Booleano',
                        'color'                  => 'Color',
                        'copyable'               => 'Copiable',
                        'copy-message'           => 'Mensaje de copia',
                        'copy-message-duration'  => 'Duración del mensaje de copia',
                        'default'                => 'Predeterminado',
                        'filterable'             => 'Filtrable',
                        'groupable'              => 'Agrupable',
                        'grow'                   => 'Expandir',
                        'icon'                   => 'Icono',
                        'icon-color'             => 'Color del icono',
                        'icon-position'          => 'Posición del icono',
                        'label'                  => 'Etiqueta',
                        'limit'                  => 'Límite',
                        'line-clamp'             => 'Límite de líneas',
                        'money'                  => 'Moneda',
                        'placeholder'            => 'Marcador de posición',
                        'prefix'                 => 'Prefijo',
                        'searchable'             => 'Buscable',
                        'size'                   => 'Tamaño',
                        'sortable'               => 'Ordenable',
                        'suffix'                 => 'Sufijo',
                        'toggleable'             => 'Conmutable',
                        'tooltip'                => 'Información sobre herramientas',
                        'vertical-alignment'     => 'Alineación vertical',
                        'vertically-align-start' => 'Alinear verticalmente al inicio',
                        'weight'                 => 'Grosor',
                        'width'                  => 'Ancho',
                        'words'                  => 'Palabras',
                        'wrap-header'            => 'Ajustar encabezado',
                        'column-span'            => 'Extensión de columna',
                        'helper-text'            => 'Texto de ayuda',
                        'hint'                   => 'Sugerencia',
                        'hint-color'             => 'Color de la sugerencia',
                        'hint-icon'              => 'Icono de la sugerencia',
                    ],

                    'datetime' => [
                        'date'              => 'Fecha',
                        'date-time'         => 'Fecha y hora',
                        'date-time-tooltip' => 'Información de fecha y hora',
                        'since'             => 'Desde',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Separador',
                        'list-with-line-breaks'    => 'Lista con saltos de línea',
                        'bulleted'                 => 'Con viñetas',
                        'limit-list'               => 'Limitar lista',
                        'expandable-limited-list'  => 'Lista limitada expandible',
                    ],

                    'select' => [
                        'separator'                => 'Separador',
                        'list-with-line-breaks'    => 'Lista con saltos de línea',
                        'bulleted'                 => 'Con viñetas',
                        'limit-list'               => 'Limitar lista',
                        'expandable-limited-list'  => 'Lista limitada expandible',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Booleano',
                        'false-icon'  => 'Icono de falso',
                        'true-icon'   => 'Icono de verdadero',
                        'true-color'  => 'Color de verdadero',
                        'false-color' => 'Color de falso',
                    ],

                    'toggle' => [
                        'boolean'     => 'Booleano',
                        'false-icon'  => 'Icono de falso',
                        'true-icon'   => 'Icono de verdadero',
                        'true-color'  => 'Color de verdadero',
                        'false-color' => 'Color de falso',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'type'           => 'Tipo',
                    'input-type'     => 'Tipo de entrada',
                    'is-multiselect' => 'Es selección múltiple',
                    'sort-order'     => 'Orden de clasificación',

                    'type-options' => [
                        'text'          => 'Entrada de texto',
                        'textarea'      => 'Área de texto',
                        'select'        => 'Selección',
                        'checkbox'      => 'Casilla de verificación',
                        'radio'         => 'Botón de opción',
                        'toggle'        => 'Interruptor',
                        'checkbox-list' => 'Lista de casillas de verificación',
                        'datetime'      => 'Selector de fecha y hora',
                        'editor'        => 'Editor de texto enriquecido',
                        'markdown'      => 'Editor de Markdown',
                        'color'         => 'Selector de color',
                    ],

                    'input-type-options' => [
                        'text'     => 'Texto',
                        'email'    => 'Correo electrónico',
                        'numeric'  => 'Numérico',
                        'integer'  => 'Entero',
                        'password' => 'Contraseña',
                        'tel'      => 'Teléfono',
                        'url'      => 'URL',
                        'color'    => 'Color',
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
            'code'       => 'Código',
            'name'       => 'Nombre',
            'type'       => 'Tipo',
            'resource'   => 'Recurso',
            'created-at' => 'Creado el',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Tipo',
            'resource' => 'Recurso',

            'type-options' => [
                'text'          => 'Entrada de texto',
                'textarea'      => 'Área de texto',
                'select'        => 'Selección',
                'checkbox'      => 'Casilla de verificación',
                'radio'         => 'Botón de opción',
                'toggle'        => 'Interruptor',
                'checkbox-list' => 'Lista de casillas de verificación',
                'datetime'      => 'Selector de fecha y hora',
                'editor'        => 'Editor de texto enriquecido',
                'markdown'      => 'Editor de Markdown',
                'color'         => 'Selector de color',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Campo restaurado',
                    'body'  => 'El campo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Campo eliminado',
                    'body'  => 'El campo se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Campo eliminado permanentemente',
                    'body'  => 'El campo se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Campos restaurados',
                    'body'  => 'Los campos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Campos eliminados',
                    'body'  => 'Los campos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Campos eliminados permanentemente',
                    'body'  => 'Los campos se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
