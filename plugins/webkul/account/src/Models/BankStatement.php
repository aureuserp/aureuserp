<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class BankStatement extends Model
{
    use HasFactory;

    protected $table = 'accounts_bank_statements';

    protected $fillable = [
        'company_id',
        'journal_id',
        'created_by',
        'name',
        'reference',
        'first_line_index',
        'date',
        'balance_start',
        'balance_end',
        'balance_end_real',
        'is_completed',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
