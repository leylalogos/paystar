<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'bank_account_id',
        'amount',
        'description',
        'reasenDescription',
        'paymentNumber'
    ];
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
