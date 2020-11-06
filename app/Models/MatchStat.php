<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchStat extends Model
{
    use HasFactory;

    public function match()
    {
        return $this->belongsTo('App\Models\Match', 'match_id');
    }
}
