<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'league_id', 'created_at', 'updated_at'];

    public function league()
    {
        return $this->belongsTo('App\Models\League', 'league_id');
    }

    public function homeMatches()
    {
        return $this->hasMany('App\Models\Match', 'home_team');
    }

    public function awayMatches()
    {
        return $this->hasMany('App\Models\Match', 'away_team');
    }
}
