<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table='transactions_table';
    public $timestamps= False;
    protected $fillable = [
        'students_id',
        'amount_paid',
        'mode_of_payment',
        'date',
        'time',

    ];
}
