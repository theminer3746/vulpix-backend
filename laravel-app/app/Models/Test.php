<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicationId',
        'androidVersion',
        'applicationVersion',
        'forced',
        'dynamicAssignedTo',
        'dynamicAssignedAt',
        'dynamicDoneAt',
    ];

    protected $casts = [
        'dynamicAssignedAt' => 'datetime',
        'dynamicDoneAt' => 'datetime',
    ];
}
