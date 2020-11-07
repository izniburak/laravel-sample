<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\LeagueService;
use App\Services\MatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeaguesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(League::withCount('teams')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:50',
            'description' => 'required|max:100',
        ]);

        $league = new League;
        $league->uuid = Str::uuid();
        $league->title = $validate['title'];
        $league->description = $validate['description'];
        $league->save();
        return response($league, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\Response
     */
    public function show(string $uuid)
    {
        $league = League::withCount('teams')->where('uuid', $uuid)->firstOrFail();
        return response($league, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $uuid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $uuid)
    {
        $validate = $request->validate([
            'title' => 'required|max:50',
            'description' => 'required|max:100',
        ]);

        $league = League::where('uuid', $uuid)->firstOrFail();
        $league->title = $validate['title'];
        $league->description = $validate['description'];
        $league->save();
        return response([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $uuid)
    {
        League::where('uuid', $uuid)->delete();
        return response([], 204);
    }

    /**
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateFixture(string $uuid)
    {
        $league = League::where('uuid', $uuid)->firstOrFail();
        /** @var $service LeagueService */
        $service = app()->make(LeagueService::class, ['league' => $league]);
        $service->generateFixture();

        return response()->json([
            'message' => 'League fixture generated.'
        ], 200);
    }

    /**
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function simulate(string $uuid)
    {
        $league = League::where('uuid', $uuid)->firstOrFail();
        /** @var $service MatchService */
        $service = app()->make(MatchService::class, ['league' => $league]);
        if (!$service->simulate()) {
            throw new \Exception("League could not be simulated.");
        };

        return response()->json([
            'message' => 'All league fixture has been simulated.'
        ], 200);
    }

    /**
     * @param Request $request
     * @param string  $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function simulateWeekly(Request $request, string $uuid)
    {
        $validate = $request->validate([
            'week' => 'required|integer',
        ]);
        $league = League::where('uuid', $uuid)->firstOrFail();
        /** @var $service MatchService */
        $service = app()->make(MatchService::class, ['league' => $league]);
        if (!$service->simulateWeekly($validate['week'])) {
            throw new \Exception("League could not be simulated weekly.");
        };

        return response()->json([
            'message' => 'League has been simulated weekly.'
        ], 200);
    }

    /**
     * @param string $uuid
     */
    public function table(string $uuid)
    {
        $league = League::where('uuid', $uuid)->firstOrFail();
        /** @var $service LeagueService */
        $service = app()->make(LeagueService::class, ['league' => $league]);
        if (!$service->table()) {
            throw new \Exception("League could not be simulated weekly.");
        };
    }
}
