<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfileImageController;
use App\Http\Controllers\AdminManagerPostController;
use App\Http\Controllers\AdminManagerRewardController;
use App\Http\Controllers\AdminManagerUserProfileController;
use App\Http\Controllers\AdminRewardController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\CartItemsController;



Route::middleware(['auth:sanctum'])->get('/user', [AuthController::class, 'index']);

Route::get('/getUserPointCounter', [AuthController::class, 'getUserPointCounter']);

Route::get('/authStore', [AuthController::class, 'index']);
Route::get('/status_user', [AuthController::class, 'getStatusUser']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/check_email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
});

Route::post('/register/check_username', function (Request $request) {
    $exists = User::where('username', $request->username)->exists();
    return response()->json(['exists' => $exists]);
});

Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::prefix('')->group(function () {
    // ********************************* Users ************************************************
Route::apiResource('/users', UserController::class);
Route::post('/update/user', [UserController::class, 'updateUser']);


// ********************************* User Followers and Popularity ************************************************
Route::post('/followers/{postUserID}/{authUserID}', [UserController::class, 'followersProfile']);
Route::post('/pop_like/{postUserID}/{authUserID}', [UserController::class, 'popLikeProfile']);

// ********************************* Users Image ************************************************
Route::post('/user/upload/image', [UserImageController::class, 'uploadUserImage']);
})->middleware('auth:sanctum');


// ********************************* UserProfiles ************************************************
Route::prefix('')->group(function () {
    Route::apiResource('/user_profiles', UserProfileController::class);
    Route::post('/update/profile', [UserProfileController::class, 'updateProfile']);
    Route::post('/user_profile/upload_image', [UserProfileImageController::class, 'uploadImageProfile']);
    Route::post('/uploadImageUserProfile', [UserProfileImageController::class, 'uploadImageUserProfile']);
})->middleware('auth:sanctum');

// ********************************* User Profile Contact ************************************************
Route::prefix('/profile')->group(function () {
    Route::apiResource('/contacts', ProfileContactController::class);
    Route::post('/newContacts', [ProfileContactController::class, 'newContact']);
})->middleware('auth:sanctum');


// ********************************* Posts ************************************************
Route::prefix('')->group(function () {
    Route::get('/postTypes', [PostController::class, 'getTypePost']);
    
    Route::apiResource('/posts', PostController::class);

    Route::post('/posts/store/{postID}', [PostController::class, 'postStore']);
    Route::post('/posts/update', [PostController::class, 'update']);
    Route::post('/posts/recoverSelected', [PostController::class, 'recoverSelected']);
    Route::post('/posts/deleteSelected', [PostController::class, 'deleteSelected']);
    Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost']);
    Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost']);

})->middleware('auth:sanctum');


// ********************************* Post Followers and Popularity *********************************
Route::prefix('/posts/popularity')->group(function () {
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);
})->middleware('auth:sanctum');


// ********************************* Reward *********************************
Route::prefix('/reward')->group(function () {
    Route::get('/getRewards', [RewardController::class, 'index']);
    Route::post('/newRewards', [RewardController::class, 'store']);
    Route::get('/show/{id}', [RewardController::class, 'show']);
    Route::put('/update/{id}', [RewardController::class, 'update']);
    Route::delete('/delete/{id}', [RewardController::class, 'destroy']);
})->middleware('auth:sanctum');


// ********************************* Cart items Reward *********************************
Route::prefix('/cartItems')->group(function () {
    Route::post('/userConfirmSelectReward', [CartItemsController::class, 'userConfirmSelectReward']);
    Route::get('/getReportReward/{userID}', [CartItemsController::class, 'getReportReward']);
    Route::post('/cancel_reward/{itemID}', [CartItemsController::class, 'cancelReward']);
})->middleware('auth:sanctum');



// ********************************* Videos *********************************
Route::prefix('/video')->group(function () {
    // Route::get('');
})->middleware('auth:sanctum');


// ********************************* Admin Manager *********************************
Route::prefix('/admin')->group(function () {


    // ********************************* Admin Post *********************************
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


    // ********************************* Admin User Profile *********************************
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

    // ********************************* Admin Reward *********************************
    Route::prefix('/rewards')->group(function () {
        Route::apiResource('/manager', AdminManagerRewardController::class);
        Route::post('/updateStatusReward/{rewardID}', [AdminManagerRewardController::class, 'updateStatusReward']);
    });


})->middleware('auth:sanctum');

// Route::apiResource('/admin_reward', AdminRewardController::class)->middleware('auth:sanctum');
