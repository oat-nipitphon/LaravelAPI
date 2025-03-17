<?php
//*

//วิธี debug ดูค่าใน controller
// dd($req->all(), $req->file('file'));
// dd($req->getContent());


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ProfileContact;
use App\Models\UserStatus;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostPopularity;
use App\Models\PostDeletetion;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfileImageController;
use App\Http\Controllers\AdminManagerPostController;
use App\Http\Controllers\AdminManagerUserProfileController;
use App\Http\Controllers\RewardController;

// User
Route::middleware(['auth:sanctum'])->get('/user', [AuthController::class, 'index']);
Route::get('/authStore', [AuthController::class, 'index']);
Route::get('/status_user', function () {
    $userStatus = UserStatus::all();
    return response()->json([
        'userStatus' => $userStatus,
    ], 200);
});


Route::post('/newContacts', [ProfileContactController::class, 'newContact']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Users
Route::apiResource('/users', UserController::class)->middleware('auth:sanctum');
Route::post('/update/user', [UserController::class, 'updateUser'])->middleware('auth:sanctum');

// Profiles
Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');
Route::post('/update/profile', [UserProfileController::class, 'updateProfile'])->middleware('auth:sanctum');

// Contact Profile
Route::prefix('/profile')->group(function () {

    Route::apiResource('/contacts', ProfileContactController::class);
})->middleware('auth:sanctum');

Route::post('/user_profile/upload_image', [UserProfileImageController::class, 'uploadImageProfile'])->middleware('auth:sanctum');
Route::post('/uploadImageUserProfile', [UserProfileImageController::class, 'uploadImageUserProfile'])->middleware('auth:sanctum');
Route::post('/user/upload/image', [UserImageController::class, 'uploadUserImage'])->middleware('auth:sanctum');


// Posts
Route::apiResource('/posts', PostController::class)->middleware('auth:sanctum');
Route::post('/posts/store/{postID}', [PostController::class, 'postStore']);
Route::post('/posts/update', [PostController::class, 'update'])->middleware('auth:sanctum');
Route::post('/posts/deleteSelected', [PostController::class, 'deleteSelected'])->middleware('auth:sanctum');

// Post type
Route::get('/postTypes', function () {
    $postTypes = App\Models\PostType::all();
    return response()->json([
        'postTypes' => $postTypes,
    ], 200);
});

// Post Recover
Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost'])->middleware('auth:sanctum');
Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost'])->middleware('auth:sanctum');


// Post Popularity
Route::prefix('/posts/popularity')->group(function () {
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);
})->middleware('auth:sanctum');


// Reward
Route::prefix('/reward')->group(function () {
    Route::get('/getRewards', [RewardController::class, 'index']);
    Route::post('/newRewards', [RewardController::class, 'store']);
    Route::get('/show/{id}', [RewardController::class, 'show']);
    Route::put('/update/{id}', [RewardController::class, 'update']);
    Route::delete('/delete/{id}', [RewardController::class, 'destroy']);
})->middleware('auth:sanctum');


// ************************************** Route ADMIN ************************************** //

Route::prefix('/admin')->group(function () {

    Route::prefix('/posts')->group(function () {
        Route::apiResource('/manager', AdminManagerPostController::class)
            ->only([
                'index',
                'create',
                'store',
                'update',
                'show',
                'destroy'
            ]);
        Route::post('/blockOrUnBlock/{postID}/{blockStatus}', [AdminManagerPostController::class, 'blockOrUnBlockPost']);
    });

    Route::prefix('/userProfiles')->group(function () {
        Route::apiResource('/manager', AdminManagerUserProfileController::class)
            ->only([
                'index',
                'create',
                'store',
                'update',
                'show',
                'destroy'
            ]);
    });
})->middleware('auth:sanctum');
