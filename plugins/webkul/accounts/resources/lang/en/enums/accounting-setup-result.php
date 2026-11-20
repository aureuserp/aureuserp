<?php

return [
    'completed'      => 'Accounting set up',
    'already-set-up' => 'Accounting is already set up',
    'no-template'    => 'No usable template company',
    'incomplete'     => 'Accounting setup was not completed',

    'descriptions' => [
        'completed'      => 'Chart of accounts, journals and default settings were created for this company.',
        'already-set-up' => 'This company already has its sale and purchase journals. Nothing was changed.',
        'no-template'    => 'This company is itself the default company used as the template, or no default company exists, so there is nothing to copy the chart of accounts, journals and default settings from. Nothing was created. Make another fully set up company the default company, then try again.',
        'incomplete'     => 'The default company used as a template is missing a sale or purchase journal, or one of its default account, journal or tax settings is empty or points at a record that does not belong to it. Nothing was created for this company. Complete the default company\'s accounting, then try again. Technical details were logged.',
    ],
];
