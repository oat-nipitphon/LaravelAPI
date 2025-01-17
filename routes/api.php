<?php
//*

//วิธี debug ดูค่าใน controller
// dd($req->all(), $req->file('file'));
// dd($req->getContent());


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\StatusUser;
use App\Models\PostType;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfileImageController;
use App\Http\Controllers\PostController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ************************************ Users ************************************ //
Route::apiResource('/users', UserController::class)->middleware('auth:sanctum');
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user_req = $request->user();
    $user_login = User::with('user_profile', 'user_profile.user_profile_images')->findOrFail($user_req->id);
    $token = $user_login->createToken($user_login->username)->plainTextToken;
    return response()->json([
        'user_login' => $user_login,
        'token' => $token
    ], 200);
});
Route::get('/status_user', function () {
    return response()->json([
        'status_user' => StatusUser::all()
    ], 200);
});
// ************************************ Users ************************************ //

// ************************************ User Profiles ************************************ //
Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');
Route::post('/user_profile/upload_image', [UserProfileController::class, 'uploadImageProfile']);
// ->middleware('auth:sanctum');
// ************************************ User Profiles ************************************ //

// ************************************ Posts ************************************ //
Route::apiResource('/posts', PostController::class)->middleware('auth:sanctum');
Route::get('/post_types', function () {
    return response()->json([
        'post_types' => PostType::all()
    ], 200);
});
// ************************************ Posts ************************************ //
