<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperPrayerSession
 */
class PrayerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leader_id',
        'mode',
        'content',
        'started_at',
        'ended_at',
        'duration_seconds',
        'proclamation_count',
        'prayer_date',
        'active_seconds',
        'paused_at',
        'is_running',

    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'paused_at' => 'datetime',
        'prayer_date' => 'date',
        'is_running' => 'boolean',

    ];

    public function leader()
    {
        return $this->belongsTo(Person::class, 'leader_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
