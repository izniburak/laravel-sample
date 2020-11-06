<?php

namespace App\Http\Controllers;

use App\Models\League;
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
}
