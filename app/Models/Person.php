<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @mixin IdeHelperPerson
 */
class Person extends Model
{
    protected $fillable = ['name', 'type', 'created_by'];

    public function fasts(){
        return $this->belongsToMany(Fast::class, 'fast_leaders', 'person_id', 'fast_id')
            ->withTimestamps();
    }
}
