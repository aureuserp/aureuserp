<?php

return [
    'action'       => 'Set up accounting for this company',
    'notice'       => 'Accounting is not set up for this company yet. Use \'Set up accounting\' below to create its chart of accounts, journals and default settings.',
    'notification' => [
        'title' => 'Accounting set up',
        'body'  => 'Chart of accounts, journals and default settings were created for this company.',
    ],
    'error' => [
        'title' => 'Accounting setup did not run',
        'body'  => 'No chart of accounts, journals or default settings were created. Make sure the default company used as a template is fully set up, then try again. Technical details were logged.',
    ],
];
