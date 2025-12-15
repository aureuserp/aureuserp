<?php

return [
    'setup' => [
        'title'   => 'Attachments',
        'tooltip' => 'Upload Attachments',

        'form' => [
            'fields' => [
                'files'                  => 'Files',
                'attachment-helper-text' => 'Max file size: 10MB. Allowed types: Images, PDF, Word, Excel, Text',

                'actions' => [
                    'delete' => [
                        'title' => 'File deleted',
                        'body'  => 'File has been deleted successfully.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Attachments Uploaded',
                    'body'  => 'Attachments uploaded successfully.',
                ],

                'warning'  => [
                    'title' => 'No new files',
                    'body'  => 'All files have already been uploaded.',
                ],

                'error' => [
                    'title' => 'Attachment upload error',
                    'body'  => 'Failed to upload attachments ',
                ],
            ],
        ],
    ],
];
