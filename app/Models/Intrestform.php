<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intrestform extends Model
{
    use HasFactory;
    protected $table='interest';
    public $timestamps= False;
    protected $fillable = [
        'name',
        'email',
        'mode_of_learning',
        'course_of_interest',
        'mode_of_payment',
        'payment_status',
        'amount_paid',
        'phone_number',
        'date',
        'time',

    ];
}
