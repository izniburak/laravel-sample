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

}
