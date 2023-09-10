<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MateController;
use App\Http\Controllers\RefreshController;
use App\Http\Controllers\RoomController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get("/", [ClassroomController::class, "index"])->name("home");

Route::get("/refresh", [RefreshController::class, "index"])->name("refresh");

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