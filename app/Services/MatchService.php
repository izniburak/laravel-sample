<?php

namespace App\Services;

use App\Models\League;
use App\Models\Match;
use App\Models\MatchStat;

class MatchService
{
    /**
     * @var League
     */
    private $league;

    public function __construct(League $league)
    {
        $this->league = $league;
    }

    /**
     * @return bool
     */
    public function simulate(): bool
    {
        $matches = $this->league->matches()->get();
        if ($matches->isEmpty()) {
            return false;
        }

        foreach ($matches as $match) {
            $this->newMatchStat($match);
        }

        return true;
    }

    /**
     * @param int $week
     *
     * @return bool
     */
    public function simulateWeekly(int $week): bool
    {
        $matches = $this->league->matches()
            ->where('week', $week)
            ->where('status', '!=', Match::MATCH_STATUS_PLAYED)->get();

        if ($matches->isEmpty()) {
            return false;
        }

        foreach ($matches as $match) {
            $this->newMatchStat($match);
        }

        return true;
    }

    /**
     * @param Match $match
     */
    private function newMatchStat(Match $match): void
    {
        // Default Stats
        $defaultValues = [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5];
        $defaultCards = [0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3];

        $stat = new MatchStat;
        $stat->match_id = $match->id;
        $stat->home_score = $defaultValues[array_rand($defaultValues)];
        $stat->away_score = $defaultValues[array_rand($defaultValues)];
        $stat->home_red_card = $defaultValues[array_rand($defaultCards)];
        $stat->away_red_card = $defaultValues[array_rand($defaultCards)];
        $stat->home_yellow_card = $defaultValues[array_rand($defaultCards)];
        $stat->away_yellow_card = $defaultValues[array_rand($defaultCards)];
        $stat->home_corner = $defaultValues[array_rand($defaultValues)];
        $stat->away_corner = $defaultValues[array_rand($defaultValues)];
        $stat->home_faul = $defaultValues[array_rand($defaultValues)];
        $stat->away_faul = $defaultValues[array_rand($defaultValues)];
        $stat->save();

        $match->status = Match::MATCH_STATUS_PLAYED;
        $match->save();
    }
}
