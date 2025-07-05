<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mpesa extends Model
{
    //
    protected $fillable = [
        'phone_number',
        'amount',
        'reference',
        'description',
        'status',
        'checkout_request_id',
        'receipt_number',
    ];
}
