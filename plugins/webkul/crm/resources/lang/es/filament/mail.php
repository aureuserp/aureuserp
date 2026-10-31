<?php

return [
    'alias' => [
        'section'             => 'Alias de correo',
        'section-description' => 'Los correos enviados a esta dirección crean leads en este equipo.',
        'name'                => 'Alias de correo',
        'name-helper'         => 'La parte antes de la @, p. ej. "ventas".',
        'name-invalid'        => 'Use solo letras, números, puntos, guiones o guiones bajos.',
        'name-taken'          => 'Este alias ya lo usa otro equipo de ventas.',
        'domain'              => 'Dominio del alias',
        'domain-helper'       => 'La parte después de la @, p. ej. "suempresa.com". El buzón de un servidor de correo entrante debe recibir los correos de este dominio.',
        'domain-invalid'      => 'Introduzca un dominio como suempresa.com.',
        'contact'             => 'Aceptar correos de',
    ],

    'reasons' => [
        'no-sender'   => 'El correo no tiene una dirección de remitente válida.',
        'auto-reply'  => 'Respuesta automática (fuera de la oficina, rebote o lista de correo).',
        'from-alias'  => 'Enviado desde uno de los alias de los equipos de ventas (bucle de correo).',
        'no-alias'    => 'No se envió a ningún alias de equipo de ventas.',
        'not-allowed' => 'El alias no permite a este remitente ("Aceptar correos de": :contact).',
    ],

    'errors' => [
        'no-imap'    => 'La extensión imap de PHP no está instalada en el servidor.',
        'connection' => 'No se pudo conectar con el servidor de correo.',
    ],

    'chatter' => [
        'from' => 'De:',
    ],

    'no-subject' => 'Correo de :email',
];
