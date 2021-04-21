<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'android_version',
        'application_version',
        'forced',
        'dynamic_assigned_to',
        'dynamic_assigned_at',
        'dynamic_done_at',
    ];

    protected $casts = [
        'dynamic_assigned_at' => 'datetime',
        'dynamic_done_at' => 'datetime',
    ];
}
