<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Post;
use App\Models\PostType;
use App\Models\PostImage;
use App\Models\PostDeletetion;
use App\Models\PostPopularity;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with([
                'postType',
                'postImage',
                'postPopularity',
                'user',
                'user.userProfile',
                'user.userProfileContact',
                'user.userProfileImage',
            ])
                ->where('deletetion_status', 'false')
                ->where('block_status', 'false')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return $post ? [

                        'id' => $post->id,
                        'title' => $post->post_title,
                        'content' => $post->post_content,
                        'userID' => $post->user_id,
                        'createdAt' => $post->created_at,
                        'updatedAtt' => $post->updated_at,

                        'postType' => $post->postType ? [
                            'id' => $post->postType->id,
                            'name' => $post->postType->type_name,
                        ] : null,

                        'postPopularity' => $post->postPopularity->map(function ($postPop) {
                            return $postPop ? [
                                'id' => $postPop->id,
                                'postID' => $postPop->post_id,
                                'userID' => $postPop->user_id,
                                'status' => $postPop->pop_status,
                            ] : null;
                        }),

                        'postImage' => $post->postImage->map(function ($image) {
                            return $image ? [
                                'id' => $image->id,
                                'imageName' => $image->image_name,
                                'imageData' => $image->image_data,
                            ] : null;
                        }),

                        'user' => $post->user ? [
                            'id' => $post->user->id,
                            'name' => $post->user->name,
                            'email' => $post->user->email,
                            'username' => $post->user->username,
                            'statusID' => $post->user->status_id
                        ] : null,

                        'userProfile' => $post->user->userProfile ? [
                            'id' => $post->user->userProfile->id,
                            'userID' => $post->user->userProfile->user_id,
                            'titleName' => $post->user->userProfile->title_name,
                            'fullName' => $post->user->userProfile->full_name,
                            'nickName' => $post->user->userProfile->nick_name,
                            'telPhone' => $post->user->userProfile->tel_phone,
                            'birthDay' => $post->user->userProfile->birth_day,
                            'createdAt' => $post->user->userProfile->created_at,
                            'updatedAt' => $post->user->userProfile->updated_at,
                        ] : null,

                        'userProfileImage' => $post->user->userProfileImage->map(function ($profileImage) {
                            return $profileImage ? [
                                'id' => $profileImage->id,
                                'imagePath' => $profileImage->image_path,
                                'imageName' => $profileImage->image_name,
                                'imageData' => $profileImage->image_data ? 'data:image/png;base64,' . base64_encode($profileImage->image_data) : null,
                            ] : null;
                        }),

                        'userProfileContact' => $post->user->userProfileContact->map(function ($contact) {
                            return $contact ? [
                                'id' => $contact->id,
                                'name' => $contact->contact_name,
                                'iconName' => $contact->contact_icon_name,
                                'iconUrl' => $contact->contact_icon_url,
                                'iconData' => $contact->contact_icon_data ? 'data:image/png;base64,'
                                    . base64_encode($contact->contact_icon_data) : null,
                            ] : null;
                        }),

                    ] : null;
                });

            if ($posts) {
                return response()->json([
                    'message' => "Laravel get posts response success.",
                    'posts' => $posts,
                ], 200);
            }

            return response()->json([
                'message' => "Laravel get posts response false",
                'posts' => $posts,
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function get posts error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function get posts error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'required|string',
                'typeID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            $post = Post::create([
                'post_title' => $validated['title'],
                'post_content' => $validated['content'],
                'refer' => $validated['refer'],
                'type_id' => $validated['typeID'],
                'user_id' => $validated['userID'],
                'deletetion_status' => "false", // status 0 == false // status 1 == true
                'block_status' => "false",
                'created_at' => $dateTimeNow,
            ]);

            if ($request->file('imageFile')) {

                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                $postImage = PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'created_at' => $dateTimeNow,
                ]);
            }

            if ($post) {
                return response()->json([
                    'message' => "laravel api store response false"
                ], 204);
            }

            return response()->json([
                'message' => 'Laravel function store successfully.',
                'post' => $post,
                'status' => 201
            ], 201);

        } catch (\Exception $error) {

            Log::error("Laravel function store error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function store error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $post = Post::with(
                'postType',
                'postImage'
            )->findOrFail($id);

            $posts = [
                'id' => $post->id,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'refer' => $post->refer,
                'userID' => $post->user_id,
                'deletetionStatus' => $post->deletetion_status,
                'blockStatus' => $post->block_status,
                'createdAt' => $post->created_at,
                'updatedAt' => $post->updated_at,
                'postType' => $post->postType ? [
                    'id' => $post->postType->id,
                    'typeName' => $post->postType->post_type_name,
                ] : null,
                'postImage' => $post->postImage->map(function ($image) {
                    return $image ? [
                        'id' => $image->id,
                        'imagePath' => $image->image_path,
                        'imageName' => $image->image_name,
                        'imageData' => $image->image_data,
                    ] : null;
                }),
            ];


            if ($posts) {
                return response()->json([
                    'message' => "Laravel function show successfully.",
                    'posts' => $posts
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function show response false !!",
                'posts' => $posts
            ], 204);

        } catch (\Exception $error) {

            Log::error("Laravel function show error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function show error",
                'error' => $error->getMessage()
            ], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'postID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'required|string',
                'typeID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            $post = Post::findOrFail($request->postID);



            if ($post) {

                $post->update([
                    'post_title' => $validated['title'],
                    'post_content' => $validated['content'],
                    'refer' => $validated['refer'],
                    'user_id' => $validated['userID'],
                    'type_id' => $validated['typeID'],
                    'updated_at' => $dateTimeNow,
                ]);

                if ($request->hasFile('imageFile')) {

                    $imageFile = $request->file('imageFile');
                    $imageData = file_get_contents($imageFile->getRealPath());
                    $imageDataBase64 = base64_encode($imageData);

                    $postImage = PostImage::where('post_id', $request->postID)->first();

                    $postImage->update([
                        'post_id' => $post->id,
                        'image_data' => $imageDataBase64,
                        'updated_at' => $dateTimeNow
                    ]);
                }
            }

            if ($post) {
                return response()->json([
                    'message' => "Laravel function update post success.",
                    'post' => $post,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function update response false",
                'postID' => $request->postID
            ], 204);

        } catch (\Exception $error) {

            Log::error("Laravel function update error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function update error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction(); // ใช้ Transaction เพื่อความปลอดภัย

            $post = Post::findOrFail($id);
            if ($post) {
                PostImage::where('post_id', $id)->delete();
                PostPopularity::where('post_id', $id)->delete();
                PostDeletetion::where('post_id', $id)->delete();
                $post->delete();
            }
            DB::commit(); // บันทึกการเปลี่ยนแปลง

            return response()->json([
                'message' => "Laravel API delete success",
            ], 200);

        } catch (\Exception $error) {

            Log::error("Laravel function destroy error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function destroy error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function postStore (string $postID) {
        try {

            if ($postID) {
                $post = Post::findOrFail($postID);

                $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

                if ($post) {

                    $post->update([
                        'deletetion_status' => 'true',
                    ]);

                    $postDeletetion = PostDeletetion::create(
                        [
                            'post_id' => $post->id,
                            'date_time_delete' => $dateTime,
                            'deletetion_status' => 'true',
                        ]
                    );

                    if ($postDeletetion) {

                        return response()->json([
                            'message' => "Post function destroy successfully.",
                            'postDeletetion' => $postDeletetion
                        ], 200);

                    } else {
                        dd($postDeletetion);
                    }

                } else {
                    dd($post);
                }

            } else {
                dd($postID);
            }

            return response()->json([
                'message' => "Post function destroy response false !!",
                'id' => $postID,
                'post' => $post
            ], 204);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel function postStore error " . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Recover get post data.
     */
    public function recoverGetPost(string $userID)
    {
        try {

            $recoverPosts = Post::with('postType')
                ->where('user_id', $userID)
                ->where('deletetion_status', 'true')
                ->where('block_status', 'false')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($recoverPosts) {

                return response()->json([
                    'message' => "Laravel function recoverGetPosts successfully.",
                    'recoverPosts' => $recoverPosts
                ], 200);
            } else {

                return response()->json([
                    'message' => "Laravel function recoverGetPosts response false !!",
                    'userID' => $userID,
                    'recoverPosts' => $recoverPosts
                ], 204);
            }
        } catch (\Exception $error) {

            Log::error("Laravel function recover post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function recover get post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Recover update status post.
     */
    public function recoverPost(string $postID)
    {
        try {

            $statusRecoverPost = Post::where('id', $postID)->first();

            if ($statusRecoverPost) {
                $statusRecoverPost->update([
                    'deletetion_status' => 'false'
                ]);
            }

            if ($statusRecoverPost) {
                return response()->json([
                    'message' => "laravel recoverPost successfullry.",
                    'post' => $statusRecoverPost
                ], 201);
            }

            return response()->json([
                'message' => "laravel recoverPost response false !!",
                'postID' => $postID,
                'post' => $statusRecoverPost
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function recover post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function recover post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Pop like post
     */
    public function postPopLike(string $userID, string $postID, string $popStatusLike)
    {
        try {
            // Check if the user already reacted
            $existingReaction = PostPopularity::where('user_id', $userID)
                ->where('post_id', $postID)
                ->first();

            if ($existingReaction) {
                // Toggle reaction
                if ($existingReaction->pop_status === $popStatusLike) {
                    $existingReaction->delete();
                } else {
                    $existingReaction->update(['pop_status' => $popStatusLike]);
                }
            } else {
                // Create new reaction
                PostPopularity::create([
                    'post_id' => $postID,
                    'user_id' => $userID,
                    'pop_status' => $popStatusLike,
                ]);
            }

            $updatedReactions = PostPopularity::where('post_id', $postID)->get();

            if ($updatedReactions) {
                return response()->json([
                    'message' => "Laravel function postPopLike successfully.",
                    'updatedReactions' => $updatedReactions
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function postPopLike response false !!",
                'updatedReactions' => $updatedReactions
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function postPopLike post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function like post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Pop dis loke post
     */
    public function postPopDisLike(string $userID, string $postID, string $popStatusDisLike)
    {
        try {
            $existingReaction = PostPopularity::where('user_id', $userID)
                ->where('post_id', $postID)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->pop_status === $popStatusDisLike) {
                    $existingReaction->delete();
                } else {
                    $existingReaction->update(['pop_status' => $popStatusDisLike]);
                }
            } else {

                PostPopularity::create([
                    'post_id' => $postID,
                    'user_id' => $userID,
                    'pop_status' => $popStatusDisLike,
                ]);
            }

            $updatedReactions = PostPopularity::where('post_id', $postID)->get();

            if ($updatedReactions) {
                return response()->json([
                    'message' => "Laravel function postPopDisLike successfully.",
                    'updatedReactions' => $updatedReactions
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function postPopDisLike response false !!",
                'updatedReactions' => $updatedReactions
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function postPopDisLike error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function dis like post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
