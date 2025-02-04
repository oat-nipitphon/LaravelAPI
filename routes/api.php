<?php
//*

//วิธี debug ดูค่าใน controller
// dd($req->all(), $req->file('file'));
// dd($req->getContent());


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\StatusUser;
use App\Models\Post;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminManagerPostController;
use App\Http\Controllers\AdminManagerUserProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageFileUploadController;

Route::apiResource('/imageFileUploads', ImageFileUploadController::class)
->only(
    'index', 'create', 'store', 'update', 'show', 'destroy'
);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user_req = $request->user();
    $user_login = User::with('userProfile', 'userProfile.userProfileImage')->findOrFail($user_req->id);
    $token = $user_login->createToken($user_login->username)->plainTextToken;
    // $user_login->user->map(function ($user) {
    //     return $user ? [
    //         'id' => $user->id,
    //         'name' => $user->name,
    //         'email' => $user->email,
    //         'username' => $user->username,
    //         'created_at' => $user->created_at,
    //         'updated_at' => $user->updated_at,
    //         'userProfile' => $user->user_profile->map(function ($userProfile) {
    //             return $userProfile ? [
    //                 'id' => $userProfile->id,
    //                 'user_id' => $userProfile->user_id,
    //                 'title_name' => $userProfile->title_name,
    //                 'full_name' => $userProfile->full_name,
    //                 'nick_name' => $userProfile->nick_name,
    //                 'tel_phone' => $userProfile->tel_name,
    //                 'birth_day' => $userProfile->birth_day,
    //                 'created_at' => $userProfile->created_at,
    //                 'updated_at' => $userProfile->updated_at,
    //             ] : null;
    //         }),
    //         'userProfileImage' => $user->user_profile->user_profile_image->map(function ($profileImage) {
    //             return $profileImage ? [
    //                 'id' => $profileImage->id,
    //                 'profile_id' => $profileImage->profile_id,
    //                 'image_path' => $profileImage->image_path,
    //                 'image_name' => $profileImage->image_name,
    //                 'image_data' => $profileImage->image_data,
    //                 'created_at' => $profileImage->created_at,
    //                 'updated_at' => $profileImage->updated_at,
    //             ] : null;
    //         }),
    //     ] : null;
    // });
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


Route::apiResource('/users', App\Http\Controllers\UserController::class)->middleware('auth:sanctum');


Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');
Route::post('/user_profile/upload_image', [UserProfileController::class, 'uploadImageProfile'])->middleware('auth:sanctum');

// Post
Route::apiResource('/posts', PostController::class);
// ->middleware('auth:sanctum');

// Post Popularity
Route::prefix('/posts/popularity')->group(function () {
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);
})->middleware('auth:sanctum');

// Post Recover
Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost'])->middleware('auth:sanctum');
Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost'])->middleware('auth:sanctum');

// Post type
Route::get('/post_types', function () {
    return response()->json([
        'post_types' => App\Models\PostType::all()
    ], 200);
});
Route::get('/get_posts', function () {
    try {

        $posts = Post::with('postType', 'postImage')->get();

        return response()->json([
            'message' => "Laravel api get posts success.",
            'posts' => $posts
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => "Laravel api get posts error",
            'error' => $e->getMessage()
        ], 401);
    }
});




// ************************************** Route ADMIN ************************************** //

Route::prefix('/admin')->group(function () {

    Route::prefix('/posts')->group(function () {
        Route::apiResource('/manager', AdminManagerPostController::class)
        ->only([
            'index', 'create', 'store', 'update', 'show', 'destroy'
        ]);
        Route::post('/blockOrUnBlock/{postID}/{blockStatus}', [AdminManagerPostController::class, 'blockOrUnBlockPost']);
    });

    Route::prefix('/userProfiles')->group(function () {
        Route::apiResource('/manager', AdminManagerUserProfileController::class)
        ->only([
            'index', 'create', 'store', 'update', 'show', 'destroy'
        ]);
    });

});
// ->middleware('auth:sanctum');
