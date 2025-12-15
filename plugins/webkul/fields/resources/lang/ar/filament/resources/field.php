<?php

return [
    'navigation' => [
        'title' => 'Custom Fields',
        'group' => 'Settings',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Name',
                    'code'              => 'code',
                    'code-helper-text'  => 'Code must start with a letter or underscore, and can only contain letters, numbers, and underscores.',
                ],
            ],

            'options' => [
                'title' => 'Options',

                'fields' => [
                    'add-option' => 'Add Option',
                ],
            ],

            'form-settings' => [
                'title' => 'Form Settings',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validations',

                        'fields' => [
                            'validation'     => 'Validation',
                            'field'          => 'Field',
                            'value'          => 'Value',
                            'add-validation' => 'Add Validation',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Additional Settings',

                        'fields' => [
                            'setting'     => 'Setting',
                            'value'       => 'Value',
                            'color'       => 'Color',
                            'add-setting' => 'Add Setting',

                            'color-options' => [
                                'danger'    => 'Danger',
                                'info'      => 'Info',
                                'primary'   => 'Primary',
                                'secondary' => 'Secondary',
                                'warning'   => 'Warning',
                                'success'   => 'Success',
                            ],

                            'grid-options' => [
                                'row'    => 'Row',
                                'column' => 'Column',
                            ],

                            'input-modes' => [
                                'text'     => 'Text',
                                'email'    => 'Email',
                                'numeric'  => 'Numeric',
                                'integer'  => 'Integer',
                                'password' => 'Password',
                                'tel'      => 'Telephone',
                                'url'      => 'URL',
                                'color'    => 'Color',
                                'none'     => 'None',
                                'decimal'  => 'Decimal',
                                'search'   => 'Search',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Greater Than',
                        'gte'                  => 'Greater Than or Equal',
                        'lt'                   => 'Less Than',
                        'lte'                  => 'Less Than or Equal',
                        'max-size'             => 'Max Size',
                        'min-size'             => 'Min Size',
                        'multiple-of'          => 'Multiple Of',
                        'nullable'             => 'Nullable',
                        'prohibited'           => 'Prohibited',
                        'prohibited-if'        => 'Prohibited If',
                        'prohibited-unless'    => 'Prohibited Unless',
                        'prohibits'            => 'Prohibits',
                        'required'             => 'Required',
                        'required-if'          => 'Required If',
                        'required-if-accepted' => 'Required If Accepted',
                        'required-unless'      => 'Required Unless',
                        'required-with'        => 'Required With',
                        'required-with-all'    => 'Required With All',
                        'required-without'     => 'Required Without',
                        'required-without-all' => 'Required Without All',
                        'rules'                => 'Custom Rules',
                        'unique'               => 'Unique',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alpha Dash',
                        'alpha-num'         => 'Alpha Numeric',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => "Doesn't End With",
                        'doesnt-start-with' => "Doesn't Start With",
                        'ends-with'         => 'Ends With',
                        'filled'            => 'Filled',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Length',
                        'mac-address'       => 'MAC Address',
                        'max-length'        => 'Max Length',
                        'min-length'        => 'Min Length',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Starts With',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'select' => [
                        'different'  => 'Different',
                        'exists'     => 'Exists',
                        'in'         => 'In',
                        'not-in'     => 'Not In',
                        'same'       => 'Same',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                    ],

                    'toggle' => [
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                    ],

                    'checkbox-list' => [
                        'in'        => 'In',
                        'max-items' => 'Max Items',
                        'min-items' => 'Min Items',
                    ],

                    'datetime' => [
                        'after'           => 'After',
                        'after-or-equal'  => 'After or Equal',
                        'before'          => 'Before',
                        'before-or-equal' => 'Before or Equal',
                    ],

                    'editor' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'markdown' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'color' => [
                        'hex-color' => 'Hex Color',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Autocapitalize',
                        'autocomplete'      => 'Autocomplete',
                        'autofocus'         => 'Autofocus',
                        'default'           => 'Default Value',
                        'disabled'          => 'Disabled',
                        'helper-text'       => 'Helper Text',
                        'hint'              => 'Hint',
                        'hint-color'        => 'Hint Color',
                        'hint-icon'         => 'Hint Icon',
                        'id'                => 'Id',
                        'input-mode'        => 'Input Mode',
                        'mask'              => 'Mask',
                        'placeholder'       => 'Placeholder',
                        'prefix'            => 'Prefix',
                        'prefix-icon'       => 'Prefix Icon',
                        'prefix-icon-color' => 'Prefix Icon Color',
                        'read-only'         => 'Read Only',
                        'step'              => 'Step',
                        'suffix'            => 'Suffix',
                        'suffix-icon'       => 'Suffix Icon',
                        'suffix-icon-color' => 'Suffix Icon Color',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Autofocus',
                        'autosize'     => 'Autosize',
                        'cols'         => 'Columns',
                        'default'      => 'Default Value',
                        'disabled'     => 'Disabled',
                        'helperText'   => 'Helper Text',
                        'hint'         => 'Hint',
                        'hintColor'    => 'Hint Color',
                        'hintIcon'     => 'Hint Icon',
                        'id'           => 'Id',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Read Only',
                        'rows'         => 'Rows',
                    ],

                    'select' => [
                        'default'                   => 'Default Value',
                        'disabled'                  => 'Disabled',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Color',
                        'hint-icon'                 => 'Hint Icon',
                        'id'                        => 'Id',
                        'loading-message'           => 'Loading Message',
                        'no-search-results-message' => 'No Search Results Message',
                        'options-limit'             => 'Options Limit',
                        'preload'                   => 'Preload',
                        'searchable'                => 'Searchable',
                        'search-debounce'           => 'Search Debounce',
                        'searching-message'         => 'Searching Message',
                        'search-prompt'             => 'Search Prompt',
                    ],

                    'radio' => [
                        'default'     => 'Default Value',
                        'disabled'    => 'Disabled',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Color',
                        'hint-icon'   => 'Hint Icon',
                        'id'          => 'Id',
                    ],

                    'checkbox' => [
                        'default'     => 'Default Value',
                        'disabled'    => 'Disabled',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Color',
                        'hint-icon'   => 'Hint Icon',
                        'id'          => 'Id',
                        'inline'      => 'Inline',
                    ],

                    'toggle' => [
                        'default'     => 'Default Value',
                        'disabled'    => 'Disabled',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Color',
                        'hint-icon'   => 'Hint Icon',
                        'id'          => 'Id',
                        'off-color'   => 'Off Color',
                        'off-icon'    => 'Off Icon',
                        'on-color'    => 'On Color',
                        'on-icon'     => 'On Icon',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Bulk Toggleable',
                        'columns'                   => 'Columns',
                        'default'                   => 'Default Value',
                        'disabled'                  => 'Disabled',
                        'grid-direction'            => 'Grid Direction',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Color',
                        'hint-icon'                 => 'Hint Icon',
                        'id'                        => 'Id',
                        'max-items'                 => 'Max Items',
                        'min-items'                 => 'Min Items',
                        'no-search-results-message' => 'No Search Results Message',
                        'searchable'                => 'Searchable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Close on Date Selection',
                        'default'                 => 'Default Value',
                        'disabled'                => 'Disabled',
                        'disabled-dates'          => 'Disabled Dates',
                        'display-format'          => 'Display Format',
                        'first-fay-of-week'       => 'First Day of Week',
                        'format'                  => 'Format',
                        'helper-text'             => 'Helper Text',
                        'hint'                    => 'Hint',
                        'hint-color'              => 'Hint Color',
                        'hint-icon'               => 'Hint Icon',
                        'hours-step'              => 'Hours Step',
                        'id'                      => 'Id',
                        'locale'                  => 'Locale',
                        'minutes-step'            => 'Minutes Step',
                        'seconds'                 => 'Seconds',
                        'seconds-step'            => 'Seconds Step',
                        'timezone'                => 'Timezone',
                        'week-starts-on-monday'   => 'Week Starts on Monday',
                        'week-starts-on-sunday'   => 'Week Starts on Sunday',
                    ],

                    'editor' => [
                        'default'      => 'Default Value',
                        'disabled'     => 'Disabled',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Color',
                        'hint-icon'    => 'Hint Icon',
                        'id'           => 'Id',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Read Only',
                    ],

                    'markdown' => [
                        'default'      => 'Default Value',
                        'disabled'     => 'Disabled',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Color',
                        'hint-icon'    => 'Hint Icon',
                        'id'           => 'Id',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Read Only',
                    ],

                    'color' => [
                        'default'     => 'Default Value',
                        'disabled'    => 'Disabled',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Color',
                        'hint-icon'   => 'Hint Icon',
                        'hsl'         => 'HSL',
                        'id'          => 'Id',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Accepted File Types',
                        'append-files'                          => 'Append Files',
                        'deletable'                             => 'Deletable',
                        'directory'                             => 'Directory',
                        'downloadable'                          => 'Downloadable',
                        'fetch-file-information'                => 'Fetch File Information',
                        'file-attachments-directory'            => 'File Attachments Directory',
                        'file-attachments-visibility'           => 'File Attachments Visibility',
                        'image'                                 => 'Image',
                        'image-crop-aspect-ratio'               => 'Image Crop Aspect Ratio',
                        'image-editor'                          => 'Image Editor',
                        'image-editor-aspect-ratios'            => 'Image Editor Aspect Ratios',
                        'image-editor-empty-fill-color'         => 'Image Editor Empty Fill Color',
                        'image-editor-mode'                     => 'Image Editor Mode',
                        'image-preview-height'                  => 'Image Preview Height',
                        'image-resize-mode'                     => 'Image Resize Mode',
                        'image-resize-target-height'            => 'Image Resize Target Height',
                        'image-resize-target-width'             => 'Image Resize Target Width',
                        'loading-indicator-position'            => 'Loading Indicator Position',
                        'move-files'                            => 'Move Files',
                        'openable'                              => 'Openable',
                        'orient-images-from-exif'               => 'Orient Images from EXIF',
                        'panel-aspect-ratio'                    => 'Panel Aspect Ratio',
                        'panel-layout'                          => 'Panel Layout',
                        'previewable'                           => 'Previewable',
                        'remove-uploaded-file-button-position'  => 'Remove Uploaded File Button Position',
                        'reorderable'                           => 'Reorderable',
                        'store-files'                           => 'Store Files',
                        'upload-button-position'                => 'Upload Button Position',
                        'uploading-message'                     => 'Uploading Message',
                        'upload-progress-indicator-position'    => 'Upload Progress Indicator Position',
                        'visibility'                            => 'Visibility',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Table Settings',

                'fields' => [
                    'use-in-table'  => 'Use in Table',
                    'setting'       => 'Setting',
                    'value'         => 'Value',
                    'color'         => 'Color',
                    'alignment'     => 'Alignment',
                    'font-weight'   => 'Font Weight',
                    'icon-position' => 'Icon Position',
                    'size'          => 'Size',
                    'add-setting'   => 'Add Setting',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Warning',
                        'success'   => 'Success',
                    ],

                    'alignment-options' => [
                        'start'   => 'Start',
                        'left'    => 'Left',
                        'center'  => 'Center',
                        'end'     => 'End',
                        'right'   => 'Right',
                        'justify' => 'Justify',
                        'between' => 'Between',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra Light',
                        'light'       => 'Light',
                        'normal'      => 'Normal',
                        'medium'      => 'Medium',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Before',
                        'after'   => 'After',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Small',
                        'small'       => 'Small',
                        'medium'      => 'Medium',
                        'large'       => 'Large',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align End',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Start',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Color',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Copy Message',
                        'copy-message-duration'  => 'Copy Message Duration',
                        'default'                => 'Default',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Icon',
                        'icon-color'             => 'Icon Color',
                        'icon-position'          => 'Icon Position',
                        'label'                  => 'Label',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Line Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Prefix',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Size',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Suffix',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Start',
                        'weight'                 => 'Weight',
                        'width'                  => 'Width',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Column Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Color',
                        'hint-icon'              => 'Hint Icon',
                    ],

                    'datetime' => [
                        'date'              => 'Date',
                        'date-time'         => 'Date Time',
                        'date-time-tooltip' => 'Date Time Tooltip',
                        'since'             => 'Since',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Infolist Settings',

                'fields' => [
                    'setting'       => 'Setting',
                    'value'         => 'Value',
                    'color'         => 'Color',
                    'font-weight'   => 'Font Weight',
                    'icon-position' => 'Icon Position',
                    'size'          => 'Size',
                    'add-setting'   => 'Add Setting',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Warning',
                        'success'   => 'Success',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'Extra Light',
                        'light'       => 'Light',
                        'normal'      => 'Normal',
                        'medium'      => 'Medium',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Before',
                        'after'   => 'After',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Small',
                        'small'       => 'Small',
                        'medium'      => 'Medium',
                        'large'       => 'Large',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align End',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Start',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Color',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Copy Message',
                        'copy-message-duration'  => 'Copy Message Duration',
                        'default'                => 'Default',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Icon',
                        'icon-color'             => 'Icon Color',
                        'icon-position'          => 'Icon Position',
                        'label'                  => 'Label',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Line Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Prefix',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Size',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Suffix',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Start',
                        'weight'                 => 'Weight',
                        'width'                  => 'Width',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Column Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Color',
                        'hint-icon'              => 'Hint Icon',
                    ],

                    'datetime' => [
                        'date'              => 'Date',
                        'date-time'         => 'Date Time',
                        'date-time-tooltip' => 'Date Time Tooltip',
                        'since'             => 'Since',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'List with Line Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit List',
                        'expandable-limited-list'  => 'Expandable Limited List',
                    ],

                    'select' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'List with Line Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit List',
                        'expandable-limited-list'  => 'Expandable Limited List',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'False Icon',
                        'true-icon'   => 'True Icon',
                        'true-color'  => 'True Color',
                        'false-color' => 'False Color',
                    ],

                    'toggle' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'False Icon',
                        'true-icon'   => 'True Icon',
                        'true-color'  => 'True Color',
                        'false-color' => 'False Color',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Settings',

                'fields' => [
                    'type'           => 'Type',
                    'input-type'     => 'Input Type',
                    'is-multiselect' => 'Is Multiselect',
                    'sort-order'     => 'Sort Order',

                    'type-options' => [
                        'text'          => 'Text Input',
                        'textarea'      => 'Textarea',
                        'select'        => 'Select',
                        'checkbox'      => 'Checkbox',
                        'radio'         => 'Radio',
                        'toggle'        => 'Toggle',
                        'checkbox-list' => 'Checkbox List',
                        'datetime'      => 'Date Time Picker',
                        'editor'        => 'Rich Text Editor',
                        'markdown'      => 'Markdown Editor',
                        'color'         => 'Color Picker',
                    ],

                    'input-type-options' => [
                        'text'     => 'Text',
                        'email'    => 'Email',
                        'numeric'  => 'Numeric',
                        'integer'  => 'Integer',
                        'password' => 'Password',
                        'tel'      => 'Telephone',
                        'url'      => 'URL',
                        'color'    => 'Color',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Resource',

                'fields' => [
                    'resource' => 'Resource',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Code',
            'name'       => 'Name',
            'type'       => 'Type',
            'resource'   => 'Resource',
            'created-at' => 'Created At',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Type',
            'resource' => 'Resource',

            'type-options' => [
                'text'          => 'Text Input',
                'textarea'      => 'Textarea',
                'select'        => 'Select',
                'checkbox'      => 'Checkbox',
                'radio'         => 'Radio',
                'toggle'        => 'Toggle',
                'checkbox-list' => 'Checkbox List',
                'datetime'      => 'Date Time Picker',
                'editor'        => 'Rich Text Editor',
                'markdown'      => 'Markdown Editor',
                'color'         => 'Color Picker',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Field restored',
                    'body'  => 'The field has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Field deleted',
                    'body'  => 'The field has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Field force deleted',
                    'body'  => 'The field has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Fields restored',
                    'body'  => 'The fields has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Fields deleted',
                    'body'  => 'The fields has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Fields force deleted',
                    'body'  => 'The fields has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
