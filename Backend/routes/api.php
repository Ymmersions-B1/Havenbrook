<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MateController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::resource('/classroom', ClassroomController::class, [
    "except" => ["index", "create", "edit"]
]);

Route::post("/room/{room}", [RoomController::class, "check"])->name("room.check");

Route::resource('/room', RoomController::class, [
    "except" => ["create", "edit"]
]);

Route::resource('/mate', MateController::class, [
    "except" => ["create", "edit", "show"]
]);