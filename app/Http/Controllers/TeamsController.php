<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
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
        return response($this->getLeague($leagueUuid)->teams()->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $leagueUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $leagueUuid)
    {
        $validate = $request->validate([
            'name' => 'required|max:50',
            'stadium' => 'required|max:50'
        ]);

        $league = $this->getLeague($leagueUuid);

        $team = new Team;
        $team->uuid = Str::uuid();
        $team->league_id = $league->id;
        $team->name = $validate['name'];
        $team->stadium = $validate['stadium'];
        $team->save();
        return response($team, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $leagueUuid
     *
     * @return mixed
     */
    public function bulk(Request $request, string $leagueUuid)
    {
        $validate = $request->validate([
            'teams' => 'required|array|min:4',
        ]);
        $teams = $validate['teams'];
        $league = $this->getLeague($leagueUuid);

        $names = array_column($teams, 'name');
        $stadiums = array_column($teams, 'stadium');

        if (count($names) !== count($stadiums)) {
            return response()->json([
                'message' => 'Team name and Stadium are required.',
            ], 400);
        }

        foreach ($teams as $team) {
            $t = new Team;
            $t->uuid = Str::uuid();
            $t->league_id = $league->id;
            $t->name = $team['name'];
            $t->stadium = $team['stadium'];
            $t->save();
        }

        return response([], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $leagueUuid
     * @param string $teamUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function show(string $leagueUuid, string $teamUuid)
    {
        $team = $this->getLeague($leagueUuid)->teams()->with('homeMatches', 'awayMatches')
                    ->where('uuid', $teamUuid)->firstOrFail();
        return response($team, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $leagueUuid
     * @param string                   $teamUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $leagueUuid, string $teamUuid)
    {
        $validate = $request->validate([
            'name' => 'required|max:50',
            'stadium' => 'required|max:50'
        ]);

        $team = $this->getLeague($leagueUuid)->teams()->where('uuid', $teamUuid)->firstOrFail();
        $team->name = $validate['name'];
        $team->stadium = $validate['stadium'];
        $team->save();
        return response([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $leagueUuid
     * @param string $teamUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $leagueUuid, string $teamUuid)
    {
        $this->getLeague($leagueUuid)->teams()->where('uuid', $teamUuid)->delete();
        return response([], 204);
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
