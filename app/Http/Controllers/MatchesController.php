<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Match;
use App\Services\MatchService;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string  $leagueUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $leagueUuid)
    {
        $week = $request->get('week') ?? null;
        $results = $this->getLeague($leagueUuid)->matches();
        if (!is_null($week) && !empty($week)) {
            $results->where('week', $week);
        }
        return response($results->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return response([], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param string $leagueUuid
     * @param string $matchUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function show(string $leagueUuid, string $matchUuid)
    {
        $team = $this->getLeague($leagueUuid)->matches()->with('stat')
                    ->where('uuid', $matchUuid)->firstOrFail();
        return response($team, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        return response([], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        return response([], 404);
    }

    /**
     * @param string $uuid
     *
     * @return mixed
     */
    private function getLeague(string $uuid)
    {
        return League::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * @param string $leagueUuid
     * @param string $matchUuid
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function simulate(string $leagueUuid, string $matchUuid)
    {
        $league = $this->getLeague($leagueUuid);
        $match = $league->matches()->where('uuid', $matchUuid)
                    ->where('status', '!=', Match::MATCH_STATUS_PLAYED)->firstOrFail();
        /** @var $service MatchService */
        $service = app()->make(MatchService::class, ['league' => $league]);
        if (!$service->simulateOne($match)) {
            throw new \Exception("Match could not be simulated. No match.");
        };

        return response()->json([
            'message' => 'Match has been simulated.'
        ], 200);
    }
}
