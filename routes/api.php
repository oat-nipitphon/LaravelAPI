<?php
//*

//วิธี debug ดูค่าใน controller
// dd($req->all(), $req->file('file'));
// dd($req->getContent());


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\StatusUser;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostPopularity;
use App\Models\PostDeletetion;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminManagerPostController;
use App\Http\Controllers\AdminManagerUserProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PostController;

// User
Route::middleware(['auth:sanctum'])->get('/user', [AuthController::class, 'index']);

Route::get('/status_user', function () {
    return response()->json([
        'userStatus' => StatusUser::all()
    ], 200);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Users
Route::apiResource('/users', App\Http\Controllers\UserController::class)->middleware('auth:sanctum');


// User Profiles
Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');
Route::post('/user_profile/upload_image', [UserProfileController::class, 'uploadImageProfile'])->middleware('auth:sanctum');


// Post type
Route::get('/postTypes', function () {
    $postTypes = App\Models\PostType::all();
    return response()->json([
        'postTypes' => $postTypes,
    ], 200);
});


// Posts
Route::apiResource('/posts', PostController::class)->middleware('auth:sanctum');
Route::post('/posts/store/{postID}', [PostController::class, 'postStore']);
Route::post('/posts/update', [PostController::class, 'update'])->middleware('auth:sanctum');

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


// Post Recover
Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost'])->middleware('auth:sanctum');
Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost'])->middleware('auth:sanctum');


// Post Popularity
Route::prefix('/posts/popularity')->group(function () {
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);
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
});
// ->middleware('auth:sanctum');
