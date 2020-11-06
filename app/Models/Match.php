<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    const MATCH_STATUS_NOT_PLAYED = 0;
    const MATCH_STATUS_PLAYED = 1;
    const MATCH_STATUS_CANCELLED = 2;

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function league()
    {
        return $this->belongsTo('App\Models\League', 'league_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo('App\Models\Team', 'home_team');
    }

    public function awayTeam()
    {
        return $this->belongsTo('App\Models\Team', 'away_team');
    }

    public function stats()
    {
        return $this->hasMany('App\Models\MatchStat', 'match_id');
    }
}
