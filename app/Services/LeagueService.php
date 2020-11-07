<?php

namespace App\Services;

use App\Models\League;
use App\Models\Match;
use App\Models\Team;
use Illuminate\Support\Str;

class LeagueService
{
    /**
     * @var League
     */
    private $league;

    public function __construct(League $league)
    {
        $this->league = $league;
    }

    public function addTeams(array $teams): bool
    {
        if (empty($teams)) {
            return false;
        }

        foreach ($teams as $team) {
            $t = new Team;
            $t->uuid = Str::uuid();
            $t->league_id = $this->league->id;
            $t->name = $team;
            $t->stadium = "{$team} Stadium";
            $t->save();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function generateFixture(): bool
    {
        $teams = $this->league->teams()->get();
        if ($teams->isEmpty() || $teams->count() % 2 !== 0) {
            logger()->error("Team count must be even number for an exact match");
            return false;
        }

        $teams = $teams->toArray();
        shuffle($teams);

        $matches = [];
        $headTeam = array_pop($teams);
        $teamsCount = count($teams);
        foreach (range(1, $teamsCount * 2) as $week) {
            if ($week <= $teamsCount) {
                $matches[$week]['home'][] = $headTeam['id'];
                $matches[$week]['away'][] = $teams[0]['id'];
                for ($i = 1; $i <= ($teamsCount - 1) / 2; $i++) {
                    $matches[$week]['home'][] = $teams[$i]['id'];
                    $matches[$week]['away'][] = $teams[$teamsCount - $i]['id'];
                }
            } else {
                $matches[$week]['home'][] = $teams[0]['id'];
                $matches[$week]['away'][] = $headTeam['id'];
                for ($i = 1; $i <= ($teamsCount - 1) / 2; $i++) {
                    $matches[$week]['home'][] = $teams[$teamsCount - $i]['id'];
                    $matches[$week]['away'][] = $teams[$i]['id'];
                }
            }
            array_push($teams, array_shift($teams));
        }

        foreach ($matches as $week => $match) {
            foreach ($match['home'] as $key => $team) {
                $m = new Match;
                $m->uuid = Str::uuid();
                $m->league_id = $this->league->id;
                $m->home_team = $team;
                $m->away_team = $match['away'][$key];
                $m->week = $week;
                $m->status = Match::MATCH_STATUS_NOT_PLAYED;
                $m->save();
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function clearMatches(): bool
    {
        $this->league->matches()->delete();
        return true;
    }

    /**
     * @return bool
     */
    public function clearTeams(): bool
    {
        $this->league->teams()->delete();
        return true;
    }

    public function table()
    {
        
    }
}
