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
        'assigned_to',
        'assigned_at',
        'dynamic_done_at',
        'static_done_at',
        'status',
        'status_dynamic',
        'status_static',
        'result_dynamic',
        'result_static',
        'requester_email',
        'uuid',
    ];

    protected $casts = [
        'forced' => 'boolean',
        'assigned_at' => 'datetime',
        'dynamic_done_at' => 'datetime',
        'static_done_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'latest_version',
    ];

    public function getLatestVersionAttribute()
    {
        $applicationTestRequests = Test::where('application_id', $this->application_id)->orderByDesc('created_at')->cursor();

        foreach ($applicationTestRequests as $app) {
            if (!is_null($app->application_version)) {
                return $app->application_version;
            }
        }

        return null;
    }
}
