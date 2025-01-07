<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\StatusUser;
use App\Models\User;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users_test_api', function () {
    return response()->json([
        'users' => User::all()
    ], 200);
});

Route::get('/status_user', function () {
    return response()->json([
        'status' => StatusUser::all()
    ], 200);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('/users', UserController::class);
// ->middleware('auth:sanctum');

Route::get('/get_users_status', function () {
    $user_status = User::with('status_user')->get();

    $formatted_user_status = $user_status->map(function ($user) {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'user_status_name' => $user->status_user ? $user->status_user->status_name : null,
        ];
    });

    return response()->json([
        'user_status' => $formatted_user_status
    ]);
});
