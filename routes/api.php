<?php
//*

//วิธี debug ดูค่าใน controller
// dd($req->all(), $req->file('file'));
// dd($req->getContent());


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\StatusUser;
use App\Models\UserProfileContact;
use App\Models\Post;
use App\Models\PostType;
use App\Models\ImageUpload;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfileImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\TestCodeController;

Route::post('/test_code/upload_image', [TestCodeController::class, 'uploadImage']);

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_your_password', [AuthController::class, 'forgetYourPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('/users', UserController::class)->middleware('auth:sanctum');

Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');

Route::apiResource('/user_profile_image/upload', UserProfileImageController::class)->middleware('auth:sanctum');



Route::apiResource('/posts', PostController::class)->middleware('auth:sanctum');

Route::prefix('/posts/populairty')->group(function () {
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);
})->middleware('auth:sanctum');

Route::get('/post_types', function () {
    return response()->json([
        'post_types' => PostType::all()
    ], 200);
});
Route::get('/get_posts', function () {
    try {

        $posts = Post::with('post_types')->get();

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

Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost'])->middleware('auth:sanctum');
Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost'])->middleware('auth:sanctum');

Route::post('/upload_image', [UserProfileImageController::class, 'uploadImage']);
Route::post('/upload_image_new', [UserProfileImageController::class, 'uploadImageNew']);
