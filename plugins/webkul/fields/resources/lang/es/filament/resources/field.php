<?php

return [
    'navigation' => [
        'title' => 'Campos Personalizados',
        'group' => 'Configuración',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'           => 'Nombre',
                    'code'           => 'Código',
                    'code-helper-text' => 'El código debe comenzar con una letra o un guion bajo, y solo puede contener letras, números y guiones bajos.',
                ],
            ],

            'options' => [
                'title' => 'Opciones',

                'fields' => [
                    'add-option' => 'Agregar Opción',
                ],
            ],

            'form-settings' => [
                'title' => 'Configuración de Formulario',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validaciones',

                        'fields' => [
                            'validation'     => 'Validación',
                            'field'          => 'Campo',
                            'value'          => 'Valor',
                            'add-validation' => 'Agregar Validación',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Configuraciones Adicionales',

                        'fields' => [
                            'setting'     => 'Configuración',
                            'value'       => 'Valor',
                            'color'       => 'Color',
                            'add-setting' => 'Agregar Configuración',

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
                                'text'    => 'Texto',
                                'email'   => 'Correo Electrónico',
                                'numeric' => 'Numérico',
                                'integer' => 'Entero',
                                'password' => 'Contraseña',
                                'tel'     => 'Teléfono',
                                'url'     => 'URL',
                                'color'   => 'Color',
                                'none'    => 'Ninguno',
                                'decimal' => 'Decimal',
                                'search'  => 'Buscar',
                                'url'     => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Mayor Que',
                        'gte'                  => 'Mayor Que o Igual',
                        'lt'                   => 'Menor Que',
                        'lte'                  => 'Menor Que o Igual',
                        'max-size'             => 'Tamaño Máximo',
                        'min-size'             => 'Tamaño Mínimo',
                        'multiple-of'          => 'Múltiplo De',
                        'nullable'             => 'Nulo Permitido',
                        'prohibited'           => 'Prohibido',
                        'prohibited-if'        => 'Prohibido Si',
                        'prohibited-unless'    => 'Prohibido a Menos Que',
                        'prohibits'            => 'Prohíbe',
                        'required'             => 'Requerido',
                        'required-if'          => 'Requerido Si',
                        'required-if-accepted' => 'Requerido Si Aceptado',
                        'required-unless'      => 'Requerido a Menos Que',
                        'required-with'        => 'Requerido Con',
                        'required-with-all'    => 'Requerido Con Todos',
                        'required-without'     => 'Requerido Sin',
                        'required-without-all' => 'Requerido Sin Todos',
                        'rules'                => 'Reglas Personalizadas',
                        'unique'               => 'Único',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alfa Guion',
                        'alpha-num'         => 'Alfa Numérico',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'No Termina Con',
                        'doesnt-start-with' => 'No Comienza Con',
                        'ends-with'         => 'Termina Con',
                        'filled'            => 'Rellenado',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Longitud',
                        'mac-address'       => 'Dirección MAC',
                        'max-length'        => 'Longitud Máxima',
                        'min-length'        => 'Longitud Mínima',
                        'regex'             => 'Expresión Regular',
                        'starts-with'       => 'Comienza Con',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Rellenado',
                        'max-length' => 'Longitud Máxima',
                        'min-length' => 'Longitud Mínima',
                    ],

                    'select' => [
                        'different' => 'Diferente',
                        'exists'    => 'Existe',
                        'in'        => 'En',
                        'not-in'    => 'No En',
                        'same'      => 'Mismo',
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
                        'max-items' => 'Máximo de Ítems',
                        'min-items' => 'Mínimo de Ítems',
                    ],

                    'datetime' => [
                        'after'          => 'Después',
                        'after-or-equal' => 'Después o Igual',
                        'before'         => 'Antes',
                        'before-or-equal' => 'Antes o Igual',
                    ],

                    'editor' => [
                        'filled'     => 'Rellenado',
                        'max-length' => 'Longitud Máxima',
                        'min-length' => 'Longitud Mínima',
                    ],

                    'markdown' => [
                        'filled'     => 'Rellenado',
                        'max-length' => 'Longitud Máxima',
                        'min-length' => 'Longitud Mínima',
                    ],

                    'color' => [
                        'hex-color' => 'Color Hexadecimal',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Mayúsculas Automáticas',
                        'autocomplete'      => 'Autocompletar',
                        'autofocus'         => 'Enfoque Automático',
                        'default'           => 'Valor por Defecto',
                        'disabled'          => 'Deshabilitado',
                        'helper-text'       => 'Texto de Ayuda',
                        'hint'              => 'Sugerencia',
                        'hint-color'        => 'Color de Sugerencia',
                        'hint-icon'         => 'Icono de Sugerencia',
                        'id'                => 'Id',
                        'input-mode'        => 'Modo de Entrada',
                        'mask'              => 'Máscara',
                        'placeholder'       => 'Marcador de Posición',
                        'prefix'            => 'Prefijo',
                        'prefix-icon'       => 'Icono de Prefijo',
                        'prefix-icon-color' => 'Color del Icono de Prefijo',
                        'read-only'         => 'Sólo Lectura',
                        'step'              => 'Paso',
                        'suffix'            => 'Sufijo',
                        'suffix-icon'       => 'Icono de Sufijo',
                        'suffix-icon-color' => 'Color del Icono de Sufijo',
                    ],

                    'textarea' => [
                        'autofocus'  => 'Enfoque Automático',
                        'autosize'   => 'Tamaño Automático',
                        'cols'       => 'Columnas',
                        'default'    => 'Valor por Defecto',
                        'disabled'   => 'Deshabilitado',
                        'helperText' => 'Texto de Ayuda',
                        'hint'       => 'Sugerencia',
                        'hintColor'  => 'Color de Sugerencia',
                        'hintIcon'   => 'Icono de Sugerencia',
                        'id'         => 'Id',
                        'placeholder' => 'Marcador de Posición',
                        'read-only'  => 'Sólo Lectura',
                        'rows'       => 'Filas',
                    ],

                    'select' => [
                        'default'             => 'Valor por Defecto',
                        'disabled'            => 'Deshabilitado',
                        'helper-text'         => 'Texto de Ayuda',
                        'hint'                => 'Sugerencia',
                        'hint-color'          => 'Color de Sugerencia',
                        'hint-icon'           => 'Icono de Sugerencia',
                        'id'                  => 'Id',
                        'loading-message'     => 'Mensaje de Carga',
                        'no-search-results-message' => 'Mensaje de Sin Resultados de Búsqueda',
                        'options-limit'       => 'Límite de Opciones',
                        'preload'             => 'Precarga',
                        'searchable'          => 'Buscable',
                        'search-debounce'     => 'Retraso de Búsqueda',
                        'searching-message'   => 'Buscando Mensaje',
                        'search-prompt'       => 'Solicitud de Búsqueda',
                    ],

                    'radio' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'id'          => 'Id',
                    ],

                    'checkbox' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'id'          => 'Id',
                        'inline'      => 'En Línea',
                    ],

                    'toggle' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'id'          => 'Id',
                        'off-color'   => 'Color Apagado',
                        'off-icon'    => 'Icono Apagado',
                        'on-color'    => 'Color Encendido',
                        'on-icon'     => 'Icono Encendido',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Alternar en Masa',
                        'columns'                   => 'Columnas',
                        'default'                   => 'Valor por Defecto',
                        'disabled'                  => 'Deshabilitado',
                        'grid-direction'            => 'Dirección de la Cuadrícula',
                        'helper-text'               => 'Texto de Ayuda',
                        'hint'                      => 'Sugerencia',
                        'hint-color'                => 'Color de Sugerencia',
                        'hint-icon'                 => 'Icono de Sugerencia',
                        'id'                        => 'Id',
                        'max-items'                 => 'Máximo de Ítems',
                        'min-items'                 => 'Mínimo de Ítems',
                        'no-search-results-message' => 'Mensaje de Sin Resultados de Búsqueda',
                        'searchable'                => 'Buscable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Cerrar al Seleccionar Fecha',
                        'default'                 => 'Valor por Defecto',
                        'disabled'                => 'Deshabilitado',
                        'disabled-dates'          => 'Fechas Deshabilitadas',
                        'display-format'          => 'Formato de Visualización',
                        'first-fay-of-week'       => 'Primer Día de la Semana',
                        'format'                  => 'Formato',
                        'helper-text'             => 'Texto de Ayuda',
                        'hint'                    => 'Sugerencia',
                        'hint-color'              => 'Color de Sugerencia',
                        'hint-icon'               => 'Icono de Sugerencia',
                        'hours-step'              => 'Paso de Horas',
                        'id'                      => 'Id',
                        'locale'                  => 'Localización',
                        'minutes-step'            => 'Paso de Minutos',
                        'seconds'                 => 'Segundos',
                        'seconds-step'            => 'Paso de Segundos',
                        'timezone'                => 'Zona Horaria',
                        'week-starts-on-monday'   => 'La Semana Comienza el Lunes',
                        'week-starts-on-sunday'   => 'La Semana Comienza el Domingo',
                    ],

                    'editor' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'id'          => 'Id',
                        'placeholder' => 'Marcador de Posición',
                        'read-only'   => 'Sólo Lectura',
                    ],

                    'markdown' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'id'          => 'Id',
                        'placeholder' => 'Marcador de Posición',
                        'read-only'   => 'Sólo Lectura',
                    ],

                    'color' => [
                        'default'     => 'Valor por Defecto',
                        'disabled'    => 'Deshabilitado',
                        'helper-text' => 'Texto de Ayuda',
                        'hint'        => 'Sugerencia',
                        'hint-color'  => 'Color de Sugerencia',
                        'hint-icon'   => 'Icono de Sugerencia',
                        'hsl'         => 'HSL',
                        'id'          => 'Id',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'        => 'Tipos de Archivo Aceptados',
                        'append-files'               => 'Adjuntar Archivos',
                        'deletable'                  => 'Eliminable',
                        'directory'                  => 'Directorio',
                        'downloadable'               => 'Descargable',
                        'fetch-file-information'     => 'Obtener Información del Archivo',
                        'file-attachments-directory' => 'Directorio de Archivos Adjuntos',
                        'file-attachments-visibility' => 'Visibilidad de Archivos Adjuntos',
                        'image'                      => 'Imagen',
                        'image-crop-aspect-ratio'    => 'Relación de Aspecto de Recorte de Imagen',
                        'image-editor'               => 'Editor de Imágenes',
                        'image-editor-aspect-ratios' => 'Relaciones de Aspecto del Editor de Imágenes',
                        'image-editor-empty-fill-color' => 'Color de Relleno Vacío del Editor de Imágenes',
                        'image-editor-mode'          => 'Modo del Editor de Imágenes',
                        'image-preview-height'       => 'Altura de Vista Previa de Imagen',
                        'image-resize-mode'          => 'Modo de Redimensionar Imagen',
                        'image-resize-target-height' => 'Altura Objetivo de Redimensionar Imagen',
                        'image-resize-target-width'  => 'Ancho Objetivo de Redimensionar Imagen',
                        'loading-indicator-position' => 'Posición del Indicador de Carga',
                        'move-files'                 => 'Mover Archivos',
                        'openable'                   => 'Abrir',
                        'orient-images-from-exif'    => 'Orientar Imágenes desde EXIF',
                        'panel-aspect-ratio'         => 'Relación de Aspecto del Panel',
                        'panel-layout'               => 'Diseño del Panel',
                        'previewable'                => 'Previsualizable',
                        'remove-uploaded-file-button-position' => 'Posición del Botón Quitar Archivo Cargado',
                        'reorderable'                => 'Reordenar',
                        'store-files'                => 'Almacenar Archivos',
                        'upload-button-position'     => 'Posición del Botón de Carga',
                        'uploading-message'          => 'Mensaje de Carga',
                        'upload-progress-indicator-position' => 'Posición del Indicador de Progreso de Carga',
                        'visibility'                 => 'Visibilidad',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Configuración de Tabla',

                'fields' => [
                    'use-in-table'  => 'Usar en Tabla',
                    'setting'       => 'Configuración',
                    'value'         => 'Valor',
                    'color'         => 'Color',
                    'alignment'     => 'Alineación',
                    'font-weight'   => 'Peso de la Fuente',
                    'icon-position' => 'Posición del Icono',
                    'size'          => 'Tamaño',
                    'add-setting'   => 'Agregar Configuración',

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
                        'justify' => 'Justificar',
                        'between' => 'Entre',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra Ligero',
                        'light'       => 'Ligero',
                        'normal'      => 'Normal',
                        'medium'      => 'Medio',
                        'semi-bold'   => 'Semi Negrita',
                        'bold'        => 'Negrita',
                        'extra-bold'  => 'Extra Negrita',
                    ],

                    'icon-position-options' => [
                        'before' => 'Antes',
                        'after'  => 'Después',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Pequeño',
                        'small'       => 'Pequeño',
                        'medium'      => 'Mediano',
                        'large'       => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'          => 'Alinear al Final',
                        'alignment'          => 'Alineación',
                        'align-start'        => 'Alinear al Inicio',
                        'badge'              => 'Insignia',
                        'boolean'            => 'Booleano',
                        'color'              => 'Color',
                        'copyable'           => 'Copiable',
                        'copy-message'       => 'Mensaje de Copia',
                        'copy-message-duration' => 'Duración del Mensaje de Copia',
                        'default'            => 'Por Defecto',
                        'filterable'         => 'Filtrable',
                        'groupable'          => 'Agrupable',
                        'grow'               => 'Crecer',
                        'icon'               => 'Icono',
                        'icon-color'         => 'Color del Icono',
                        'icon-position'      => 'Posición del Icono',
                        'label'              => 'Etiqueta',
                        'limit'              => 'Límite',
                        'line-clamp'         => 'Línea de Sujeción',
                        'money'              => 'Moneda',
                        'placeholder'        => 'Marcador de Posición',
                        'prefix'             => 'Prefijo',
                        'searchable'         => 'Buscable',
                        'size'               => 'Tamaño',
                        'sortable'           => 'Ordenable',
                        'suffix'             => 'Sufijo',
                        'toggleable'         => 'Alternable',
                        'tooltip'            => 'Información sobre Herramientas',
                        'vertical-alignment' => 'Alineación Vertical',
                        'vertically-align-start' => 'Alinear Verticalmente al Inicio',
                        'weight'             => 'Peso',
                        'width'              => 'Ancho',
                        'words'              => 'Palabras',
                        'wrap-header'        => 'Envolver Encabezado',
                        'column-span'        => 'Extensión de Columna',
                        'helper-text'        => 'Texto de Ayuda',
                        'hint'               => 'Sugerencia',
                        'hint-color'         => 'Color de Sugerencia',
                        'hint-icon'          => 'Icono de Sugerencia',
                    ],

                    'datetime' => [
                        'date'              => 'Fecha',
                        'date-time'         => 'Fecha y Hora',
                        'date-time-tooltip' => 'Información sobre Herramientas de Fecha y Hora',
                        'since'             => 'Desde',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Configuración de Lista de Información',

                'fields' => [
                    'setting'       => 'Configuración',
                    'value'         => 'Valor',
                    'color'         => 'Color',
                    'font-weight'   => 'Peso de la Fuente',
                    'icon-position' => 'Posición del Icono',
                    'size'          => 'Tamaño',
                    'add-setting'   => 'Agregar Configuración',

                    'color-options' => [
                        'danger'    => 'Peligro',
                        'info'      => 'Información',
                        'primary'   => 'Primario',
                        'secondary' => 'Secundario',
                        'warning'   => 'Advertencia',
                        'success'   => 'Éxito',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra Ligero',
                        'light'       => 'Ligero',
                        'normal'      => 'Normal',
                        'medium'      => 'Mediano',
                        'semi-bold'   => 'Semi Negrita',
                        'bold'        => 'Negrita',
                        'extra-bold'  => 'Extra Negrita',
                    ],

                    'icon-position-options' => [
                        'before' => 'Antes',
                        'after'  => 'Después',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Pequeño',
                        'small'       => 'Pequeño',
                        'medium'      => 'Mediano',
                        'large'       => 'Grande',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'          => 'Alinear al Final',
                        'alignment'          => 'Alineación',
                        'align-start'        => 'Alinear al Inicio',
                        'badge'              => 'Insignia',
                        'boolean'            => 'Booleano',
                        'color'              => 'Color',
                        'copyable'           => 'Copiable',
                        'copy-message'       => 'Mensaje de Copia',
                        'copy-message-duration' => 'Duración del Mensaje de Copia',
                        'default'            => 'Por Defecto',
                        'filterable'         => 'Filtrable',
                        'groupable'          => 'Agrupable',
                        'grow'               => 'Crecer',
                        'icon'               => 'Icono',
                        'icon-color'         => 'Color del Icono',
                        'icon-position'      => 'Posición del Icono',
                        'label'              => 'Etiqueta',
                        'limit'              => 'Límite',
                        'line-clamp'         => 'Línea de Sujeción',
                        'money'              => 'Moneda',
                        'placeholder'        => 'Marcador de Posición',
                        'prefix'             => 'Prefijo',
                        'searchable'         => 'Buscable',
                        'size'               => 'Tamaño',
                        'sortable'           => 'Ordenable',
                        'suffix'             => 'Sufijo',
                        'toggleable'         => 'Alternable',
                        'tooltip'            => 'Información sobre Herramientas',
                        'vertical-alignment' => 'Alineación Vertical',
                        'vertically-align-start' => 'Alinear Verticalmente al Inicio',
                        'weight'             => 'Peso',
                        'width'              => 'Ancho',
                        'words'              => 'Palabras',
                        'wrap-header'        => 'Envolver Encabezado',
                        'column-span'        => 'Extensión de Columna',
                        'helper-text'        => 'Texto de Ayuda',
                        'hint'               => 'Sugerencia',
                        'hint-color'         => 'Color de Sugerencia',
                        'hint-icon'          => 'Icono de Sugerencia',
                    ],

                    'datetime' => [
                        'date'              => 'Fecha',
                        'date-time'         => 'Fecha y Hora',
                        'date-time-tooltip' => 'Información sobre Herramientas de Fecha y Hora',
                        'since'             => 'Desde',
                    ],

                    'checkbox-list' => [
                        'separator'           => 'Separador',
                        'list-with-line-breaks' => 'Lista con Saltos de Línea',
                        'bulleted'            => 'Con Viñetas',
                        'limit-list'          => 'Limitar Lista',
                        'expandable-limited-list' => 'Lista Limitada Expandible',
                    ],

                    'select' => [
                        'separator'           => 'Separador',
                        'list-with-line-breaks' => 'Lista con Saltos de Línea',
                        'bulleted'            => 'Con Viñetas',
                        'limit-list'          => 'Limitar Lista',
                        'expandable-limited-list' => 'Lista Limitada Expandible',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Booleano',
                        'false-icon'  => 'Icono Falso',
                        'true-icon'   => 'Icono Verdadero',
                        'true-color'  => 'Color Verdadero',
                        'false-color' => 'Color Falso',
                    ],

                    'toggle' => [
                        'boolean'     => 'Booleano',
                        'false-icon'  => 'Icono Falso',
                        'true-icon'   => 'Icono Verdadero',
                        'true-color'  => 'Color Verdadero',
                        'false-color' => 'Color Falso',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'type'         => 'Tipo',
                    'input-type'   => 'Tipo de Entrada',
                    'is-multiselect' => 'Es Multiselección',
                    'sort-order'   => 'Orden de Clasificación',

                    'type-options' => [
                        'text'          => 'Entrada de Texto',
                        'textarea'      => 'Área de Texto',
                        'select'        => 'Seleccionar',
                        'checkbox'      => 'Casilla de Verificación',
                        'radio'         => 'Radio',
                        'toggle'        => 'Alternar',
                        'checkbox-list' => 'Lista de Casillas de Verificación',
                        'datetime'      => 'Selector de Fecha y Hora',
                        'editor'        => 'Editor de Texto Enriquecido',
                        'markdown'      => 'Editor de Markdown',
                        'color'         => 'Selector de Color',
                    ],

                    'input-type-options' => [
                        'text' => 'Texto',
                    ],
                ],
            ],
        ],
    ],
];
