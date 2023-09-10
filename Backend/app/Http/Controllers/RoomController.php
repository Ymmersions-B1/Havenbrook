<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\Mate;
use App\Models\Room;
use GuzzleHttp\Client;
use App\Models\Classroom;
use App\Events\RefreshEvent;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Room::with("mates")->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $classroom = Classroom::find($request->classroom);

        $room = Room::create([
            "title" => $request->title,
            "uuid" => uniqid($request->classroom . "_"),
            "completed" => false
        ]);

        if ($request->mates) {
            foreach ($request->mates as $key => $value) {
                $mate = Mate::create([
                    "name" => $value
                ]);

                $room->mates()->attach($mate->id);
            }
        }


        if ($request->classroom && $classroom != null) {
            $classroom->rooms()->attach($room->id);
        }


        $client = new Client();
        $res = $client->get(env("GENERATOR_URL") . "/?uuid=" . $room->uuid);

        abort_if($res->getStatusCode() != 200, 500, "Erreur avec la génération de fihier !");

        $response = json_decode($res->getBody());

        $room->file = $response->file;
        $room->save();

        foreach ($response->passwords as $password) {
            $code = Code::create([
                "code" => $password,
            ]);

            $room->codes()->attach($code);
        }

        event(new RefreshEvent("refresh"));

        return redirect()->route("room.show", $room);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return view("rooms.show",  [
            "room" => $room->load("mates"),
            "foundedCodes" => $room->codes->where("founded", true),
            "progress" => $room->calculateFoundPercentage()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $room->update([
            "name" => $request->name
        ]);

        return response()->json($room->load("mates"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        foreach ($room->mates as $key => $value) {
            $room->mates()->detach($value->id);
        }

        $room->delete();
        
        event(new RefreshEvent("refresh"));

        return response()->json(Room::with("mates")->get());
    }

    public function check(Request $request, Room $room) {
        $room->check($request->code);

        return redirect()->route("room.show", $room);
    }
}
