<?php

return [
    'title'       => 'Contact Us',
    'intro'       => 'Contact us about anything related to our company or services.',
    'intro-reply' => 'We\'ll do our best to get back to you as soon as possible.',
    'submit'      => 'Submit',
    'lead-note'   => 'Created from the website Contact Us form.',

    'fields' => [
        'name'     => 'Name',
        'phone'    => 'Phone Number',
        'email'    => 'Email',
        'company'  => 'Company',
        'subject'  => 'Subject',
        'question' => 'Question',
    ],

    'validation' => [
        'no-markup' => 'Please enter plain text only. HTML, scripts and links like javascript: are not allowed.',
        'phone'     => 'Please enter a valid phone number (digits, spaces, +, -, brackets).',
    ],

    'notifications' => [
        'failed'   => 'Your message could not be sent. Please try again later.',
        'too-many' => 'Too many messages. Please try again in :minutes minute(s).',

        'sent' => [
            'title' => 'Thank you!',
            'body'  => 'Your message has been sent. We will get back to you soon.',
        ],
    ],

    'mail' => [
        'subject' => 'Website contact: :subject',
        'intro'   => 'A new message was sent from the website Contact Us form.',
    ],
];
