<?php

namespace Webkul\Website\Models;

use Webkul\Partner\Models\Partner as BasePartner;

class Partner extends BasePartner
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'password',
            'is_active',
        ]);

        $this->mergeCasts([
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
        ]);

        parent::__construct($attributes);
    }
}
