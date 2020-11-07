<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $leagueUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $leagueUuid)
    {
        return response($this->getLeague($leagueUuid)->matches()->get(), 200);
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
        $team = $this->getLeague($leagueUuid)->matches()->with('stats')
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
}
