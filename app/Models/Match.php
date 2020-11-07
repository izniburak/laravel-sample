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

    protected $hidden = ['id', 'league_id', 'home_team', 'away_team', 'created_at', 'updated_at'];

    protected $appends = [ 'home', 'away' ];

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

    public function stat()
    {
        return $this->hasOne('App\Models\MatchStat', 'match_id');
    }

    public function getHomeAttribute()
    {
        $team = $this->homeTeam()->first();
        return [
            'uuid' => $team->uuid,
            'name' => $team->name,
        ];
    }

    public function getAwayAttribute()
    {
        $team = $this->awayTeam()->first();
        return [
            'uuid' => $team->uuid,
            'name' => $team->name,
        ];
    }

    public function getStatusAttribute($value)
    {
        $status = 'Not played';
        if ($value === Match::MATCH_STATUS_PLAYED) {
            $status = 'Played';
        } elseif ($value === Match::MATCH_STATUS_CANCELLED) {
            $status = 'Cancelled';
        }
        return $status;
    }
}
