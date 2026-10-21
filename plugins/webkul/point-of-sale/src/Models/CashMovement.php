<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Move;
use Webkul\PointOfSale\Database\Factories\CashMovementFactory;
use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class CashMovement extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'pos_cash_movements';

    protected $fillable = [
        'type',
        'amount',
        'reason',
        'moved_at',
        'session_id',
        'move_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'type'     => CashMovementType::class,
        'amount'   => 'decimal:4',
        'moved_at' => 'datetime',
    ];

    protected $attributes = [
        'amount' => 0,
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function signedAmount(): float
    {
        return $this->type->signedAmount((float) $this->amount);
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeUserId(): void
    {
        $this->user_id ??= Auth::id();
    }

    public function computeSessionDefaults(): void
    {
        $session = $this->session;

        if (! $session) {
            return;
        }

        $this->company_id ??= $session->company_id;
    }

    public function computeMovedAt(): void
    {
        $this->moved_at ??= now();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (CashMovement $cashMovement) {
            $cashMovement->computeCreatorId();

            $cashMovement->computeUserId();

            $cashMovement->computeSessionDefaults();

            $cashMovement->computeMovedAt();
        });
    }

    protected static function newFactory(): CashMovementFactory
    {
        return CashMovementFactory::new();
    }
}
