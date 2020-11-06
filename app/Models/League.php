<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'league_id');
    }

    public function matches()
    {
        return $this->hasMany('App\Models\Match', 'league_id');
    }
}
