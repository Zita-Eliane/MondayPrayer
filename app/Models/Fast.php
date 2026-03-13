<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @mixin IdeHelperFast
 */
class Fast extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_user_id',
        'leader_id',
        'fast_date',
        'fast_type',
        'prayer_minutes',
    ];

    // Relation vers l'utilisateur qui jeûne
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_user_id');
    }

    public function leader()
    {
        return $this->belongsTo(Person::class, 'leader_id');
    }

    public function leaders()
    {
        return $this->belongsToMany(Person::class, 'fast_leaders', 'fast_id', 'person_id')
        ->withTimestamps();
    }

}
