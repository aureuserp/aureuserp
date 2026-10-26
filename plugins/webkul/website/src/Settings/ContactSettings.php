<?php

namespace Webkul\Website\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public ?string $email;

    public ?string $phone;

    public ?string $twitter;

    public ?string $facebook;

    public ?string $instagram;

    public ?string $whatsapp;

    public ?string $youtube;

    public ?string $linkedin;

    public ?string $pinterest;

    public ?string $tiktok;

    public ?string $github;

    public ?string $slack;

    public bool $contact_form_enabled;

    public string $contact_form_action;

    public static function group(): string
    {
        return 'website_contact';
    }
}
