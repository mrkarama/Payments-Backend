<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'course_name',
        'course_price',
        'country_code',
        'transaction_reference',
    ];
}
