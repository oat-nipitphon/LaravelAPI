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
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user_req = $request->user();
    $user_login = User::with('userProfile', 'userProfileImage')->findOrFail($user_req->id);
    $token = $user_login->createToken($user_login->username)->plainTextToken;

    $user_login = [
        'id' => $user_login->id,
        'name' => $user_login->name,
        'email' => $user_login->email,
        'username' => $user_login->username,
        'status_id' => $user_login->status_id,
        'created_at' => $user_login->created_at,
        'updated_at' => $user_login->updated_at,
        'userProfile' => $user_login->userProfile ? [
            'id' => $user_login->userProfile->id,
            'user_id' => $user_login->userProfile->user_id,
            'title_name' => $user_login->userProfile->title_name,
            'full_name' => $user_login->userProfile->full_name,
            'nick_name' => $user_login->userProfile->nick_name,
            'tel_phone' => $user_login->userProfile->tel_name,
            'birth_day' => $user_login->userProfile->birth_day,
            'created_at' => $user_login->userProfile->created_at,
            'updated_at' => $user_login->userProfile->updated_at,
        ] : null,
        'userProfileImage' => $user_login->userProfileImage->map(function ($image) {
            return $image ? [
                'id' => $image->id,
                'imageData' => 'data:image/png;base64,'. base64_encode($image->image_data),
            ] : null;
        }),
    ];

    return response()->json([
        'user_login' => $user_login,
        'token' => $token
    ], 200);
});

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
Route::apiResource('/posts', PostController::class);
// ->middleware('auth:sanctum');

Route::delete('/posts/confirmDelete/{postID}', function ($postID) {
    DB::beginTransaction(); // ใช้ Transaction เพื่อความปลอดภัย
    $post = Post::findOrFail($postID);
    if ($post) {
        PostImage::where('post_id', $postID)->delete();
        PostPopularity::where('post_id', $postID)->delete();
        PostDeletetion::where('post_id', $postID)->delete();
        $post->delete();
    }
    DB::commit(); // บันทึกการเปลี่ยนแปลง
    return response()->json([
        'message' => "Laravel API delete success",
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
