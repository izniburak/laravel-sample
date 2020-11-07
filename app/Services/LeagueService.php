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

    /**
     * Table
     */
    public function table(): array
    {
        $teams = $this->league->teams()->get();

        $table = [];
        /** @var Team $team */
        foreach ($teams as $team) {
            $homeMatch = $team->homeMatches()->where('status', Match::MATCH_STATUS_PLAYED)->get();
            $awayMatch = $team->awayMatches()->where('status', Match::MATCH_STATUS_PLAYED)->get();
            $matches = $homeMatch->merge($awayMatch);
            $total = [
                'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
                'goal_for' => 0, 'goal_against' => 0, 'goal_difference' => 0, 'points' => 0,
            ];
            foreach ($matches as $match) {
                $stat = $match->stat()->first();
                $total['played']++;
                $point = 0;
                if ($stat->home_score > $stat->away_score) {
                    $total['won']++;
                    $point = 3;
                } elseif ($stat->home_score === $stat->away_score) {
                    $total['drawn']++;
                    $point = 1;
                } else {
                    $total['lost']++;
                }
                $total['goal_for'] += $stat->home_score;
                $total['goal_against'] += $stat->away_score;
                $total['points'] += $point;
            }
            $total['goal_difference'] = $total['goal_for'] - $total['goal_against'];
            $table[] = array_merge(['name' => $team->name], $total);
        }

        $table = collect($table)->sortByDesc('points')->values()->all();

        return $table;
    }
}
