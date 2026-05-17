<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'date',
        'orders_count',
        'total_sales',
    ];

    protected $casts = [
        'date' => 'date',
        'orders_count' => 'integer',
        'total_sales' => 'decimal:2',
    ];
}
