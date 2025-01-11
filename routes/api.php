<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TestCodeController;
use App\Models\StatusUser;
use App\Models\User;
use App\Models\UserProfileContact;
use App\Http\Controllers\ImageUploadController;
use App\Models\ImageUpload;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request)
{
    $user_req = $request->user();
    $user_login = User::with('user_profile')->findOrFail($user_req->id);
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('/users', UserController::class)
    ->middleware('auth:sanctum');

Route::apiResource('/posts', PostController::class)
    ->middleware('auth:sanctum');

Route::apiResource('/user_profiles', UserProfileController::class)
    ->middleware('auth:sanctum');

Route::post('/user_profile/upload_image_profile',
    [UserProfileController::class, 'uploadImageUserProfile']);

Route::post('/upload_image', [ImageUploadController::class, 'uploadImage']);

// Route::get('/status_user', function () {
//     $user_status = User::with('status_user')->get();

//     $formatted_user_status = $user_status->map(function ($user) {
//         return [
//             'id' => $user->id,
//             'email' => $user->email,
//             'user_status_name' => $user->status_user ? $user->status_user->status_name : null,
//         ];
//     });

//     return response()->json([
//         'user_status' => $formatted_user_status
//     ]);
// });

// Route::get('/users_test_api', function () {
//     try {

//         $users = User::with(
//             'status_user',
//             'user_profile',
//             'user_profile.user_profile_contacts',
//             'user_profile.user_profile_images',
//             'user_logins',
//             'posts.post_types',
//             )->get();

//         return response()->json([
//             'users' => $users
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => "Laravel route users test api error",
//             'error' => $e->getMessage()
//         ], 401);
//     }
// });
