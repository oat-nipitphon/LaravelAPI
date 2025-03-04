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

// User
Route::middleware(['auth:sanctum'])->get('/user', [AuthController::class, 'index']);
Route::get('/authStore', [AuthController::class, 'index']);
Route::get('/status_user', function () {
    $userStatus = UserStatus::all();
    return response()->json([
        'userStatus' => $userStatus,
    ], 200);
});

Route::get('/testContact', function () {
    $user = User::with([
        'userProfile',
        'userProfile.profileContact' // ใช้ชื่อให้ตรงกับที่กำหนดใน Model
    ])->get()->map(function ($row) {
        return [
            'id' => $row->id,
            'name' => $row->name,
            'userProfile' => $row->userProfile ? [
                'fullName' => $row->userProfile->full_name
            ] : null,
            'profileContacts' => $row->userProfile ?
            $row->userProfile->profileContact->map(function ($contact) {
                return [
                    'name' => $contact->name,
                    'url' => $contact->url,
                    'icon' => $contact->icon_data
                ];
            }) : [],
        ];
    });

    return response()->json([
        'message' => "api profile contact success",
        'user' => $user
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

// User Profiles
Route::apiResource('/user_profiles', UserProfileController::class)->middleware('auth:sanctum');
Route::post('/update/user_profiles', [ UserProfileController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/user_profile/upload_image', [UserProfileImageController::class, 'uploadImageProfile'])->middleware('auth:sanctum');
Route::post('/uploadImageUserProfile', [UserProfileImageController::class, 'uploadImageUserProfile'])->middleware('auth:sanctum');
Route::post('/user/upload/image', [UserImageController::class, 'uploadUserImage'])->middleware('auth:sanctum');


// Posts
Route::apiResource('/posts', PostController::class);
// ->middleware('auth:sanctum');
Route::post('/posts/store/{postID}', [PostController::class, 'postStore']);
Route::post('/posts/update', [PostController::class, 'update'])->middleware('auth:sanctum');
// Post type
Route::get('/postTypes', function () {
    $postTypes = App\Models\PostType::all();
    return response()->json([
        'postTypes' => $postTypes,
    ], 200);
});

// Editor TipTap
Route::post('/EditorTipTap/NewPost', function (Request $req) {
    $req->validate([
        'userID' => 'required|integer',
        'title' => 'required|string',
        'content' => 'required|string',
        'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    if (!empty($req->content)) {
        if ($req->hasFile('imageFile')) {
            $imageFile = $req->file('imageImage');
            $imageName = $imageFile->getClientOriginalName();
            dd([
                'content' => $req->title . $req->content,
                'imageFile' => $req->file('imageFile'),
                'imageName' => $imageName
            ]);
        }
    } else {
        dd($req);
    }
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
})->middleware('auth:sanctum');
