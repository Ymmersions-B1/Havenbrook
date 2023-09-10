<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::with('rooms.mates')->get();
        $finalPercent = $classrooms->avg(function ($classroom) {
            return $classroom->calculateOverallFoundPercentage();
        });

        return view("index", [
            "classrooms" => $classrooms,
            "message" => $finalPercent >= env("WIN_MIN") ? "You won 🎉" : "You lost 🥲",
            "end" => Carbon::parse(env("END_DATE"))->timestamp
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $classroom = Classroom::create([
            "name" => $request->name
        ]);

        return response()->json($classroom);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        return view("classrooms.show", [
            "classroom" => $classroom->load("rooms")
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $classroom->update([
            "name" => $request->name,
        ]);

        return response()->json($classroom->load("rooms"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return response()->json();
    }
}
