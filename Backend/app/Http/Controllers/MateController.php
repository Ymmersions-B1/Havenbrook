<?php

namespace App\Http\Controllers;

use App\Models\Mate;
use App\Models\Room;
use App\Events\RefreshEvent;
use Illuminate\Http\Request;

class MateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Mate::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mate = Mate::create([
            "name" => $request->name,
        ]);

        $room = Room::find($request->room);

        $room->mates()->attach($mate->id);
        
        event(new RefreshEvent("refresh"));

        return redirect()->route("room.show", $room->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mate $mate)
    {
        if($request->name) {
            $mate->name = $request->name;
        }

        if ($request->room) {
            Room::find($mate->getRoom()->id)->mates()->detach($mate->id);

            Room::find($request->room)->mates()->attach($mate->id);
        }

        return response()->json($mate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mate $mate)
    {
        $room = $mate->getRoom();

        Room::find($room->id)->mates()->detach($mate->id);
        
        $mate->delete();
        
        event(new RefreshEvent("refresh"));

        return redirect()->route("room.show", $room);
    }
}
