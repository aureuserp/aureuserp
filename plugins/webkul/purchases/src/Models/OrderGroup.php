<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Purchase\Database\Factories\OrderGroupFactory;
use Webkul\Security\Models\User;

class OrderGroup extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'purchases_order_groups';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'creator_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): OrderGroupFactory
    {
        return OrderGroupFactory::new();
    }
}
