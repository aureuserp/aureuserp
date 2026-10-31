<?php

return [
    'title'       => 'Contáctenos',
    'intro'       => 'Contáctenos sobre cualquier tema relacionado con nuestra empresa o servicios.',
    'intro-reply' => 'Haremos lo posible por responderle lo antes posible.',
    'submit'      => 'Enviar',
    'lead-note'   => 'Creado desde el formulario Contáctenos del sitio web.',

    'fields' => [
        'name'     => 'Nombre',
        'phone'    => 'Número de teléfono',
        'email'    => 'Correo electrónico',
        'company'  => 'Empresa',
        'subject'  => 'Asunto',
        'question' => 'Pregunta',
    ],

    'validation' => [
        'no-markup' => 'Introduzca solo texto plano. No se permiten HTML ni scripts.',
        'phone'     => 'Introduzca un número de teléfono válido (dígitos, espacios, +, -, paréntesis).',
    ],

    'notifications' => [
        'failed'   => 'No se pudo enviar su mensaje. Inténtelo más tarde.',
        'too-many' => 'Demasiados mensajes. Inténtelo de nuevo en :minutes minuto(s).',

        'sent' => [
            'title' => '¡Gracias!',
            'body'  => 'Su mensaje ha sido enviado. Le responderemos pronto.',
        ],
    ],

    'mail' => [
        'subject' => 'Contacto del sitio web: :subject',
        'intro'   => 'Se envió un nuevo mensaje desde el formulario Contáctenos del sitio web.',
    ],
];
