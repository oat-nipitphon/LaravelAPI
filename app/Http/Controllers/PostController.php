<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostDeletetion;
use App\Models\PostPopularity;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Faker\Core\DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            // Status 0=false, 1=true
            $getPosts = Post::with([
                'postType',
                'postImage',
                'postPopularity',
                'user',
                'user.userProfile.userProfileImage',
            ])
                ->where('deletetion_status', false)
                ->where('block_status', false)
                ->orderBy('created_at', 'desc')
                ->get();

                // dd($getPosts);


            $posts = $getPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->post_title,
                    'content' => $post->post_content,
                    'userID' => $post->user_id,
                    'createdAt' => $post->created_at,
                    'updatedAt' => $post->updated_at,

                    'postType' => $post->postType ? [
                        'id' => $post->postType->id,
                        'name' => $post->postType->post_type_name,
                    ] : null,

                    'postPopularity' => $post->postPopularity->map(function ($pop) {
                        return [
                            'id' => $pop->id,
                            'status' => $pop->pop_status,
                            'postID' => $pop->post_id,
                            'userID' => $pop->user_id,
                        ];
                    }),

                    'postImage' => $post->postImage->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'imageData' => $image->image_data ? 'data:image/png;base64,'
                            . base64_encode($image->image_data) : null,
                        ];
                    }),

                    'user' => $post->user ? [
                        'id' => $post->user->id,
                        'username' => $post->user->username,
                    ] : null,

                    'userProfile' => $post->user && $post->user->userProfile ? [
                        'id' => $post->user->userProfile->id,
                        'fullName' => $post->user->userProfile->full_name,
                    ] : null,

                    'userProfileImage' => $post->user->userProfile->userProfileImage->map(function ($userImage) {
                        return [
                            'id' => $userImage->id,
                            'imageData' => $userImage->image_data ? 'data:image/png;base64,'
                            . base64_encode($userImage->image_data) : null,
                        ];
                    }),
                ];
            });

            return response()->json([
                'message' => "Laravel API get posts success.",
                'posts' => $posts,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel api get posts error",
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'required|string',
                'typeID' => 'required|string',
                'image' => 'nullable|image|max:2048'
            ]);

            $dateTimeCreatePost = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            $post = Post::create([
                'post_title' => $validated['title'],
                'post_content' => $validated['content'],
                'refer' => $validated['refer'],
                'type_id' => $validated['typeID'],
                'user_id' => $validated['userID'],
                'deletetion_status' => 'false', // status 0 == false // status 1 == true
                'block_status' => 'false',
                'created_at' => $dateTimeCreatePost,
            ]);

            if ($request->hasFile('image')) {
                $postImage = new PostImage();
                $file = $request->file('image');
                $imgPath = $file->store('post_images', 'public');
                $imgName = $file->getClientOriginalName();
                $imgNameNew = time() . "_" . $imgName;
                $imgData = file_get_contents($file->getRealPath());
                $imgDataBase64 = base64_encode($imgData);
                $postImage->create([
                    'post_id' => $post->id,
                    'image_path' => $imgPath,
                    'image_name' => $imgNameNew,
                    'image_data' => $imgDataBase64,
                ]);
            }

            if ($post && $postImage) {

                return response()->json([
                    'message' => 'Post created successfully!',
                    'post' => $post,
                    'postImage' => $imgDataBase64
                ], 201);
            }

        } catch (\Exception $e) {

            Log::error('Error in storing post: ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => "laravel post controller function store error :",
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $post = Post::with('postType', 'user')->findOrFail($id);

            if (!$post) {
                return response()->json([
                    'message' => "laravel api post No content !.",
                    'post' => "ID-" . $id . " - " . $post
                ], 204);
            }


            return response()->json([
                'message' => "Laravel api get posts success.",
                'post' => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel api get posts error",
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $request->validate([
                'postID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'required|string',
                'typeID' => 'required|integer',
                'userID' => 'required|integer',
            ]);

            $post = Post::findOrFail($request->postID);


            if ($post) {

                $post->update([
                    'post_title' => $request->title,
                    'post_content' => $request->content,
                    'type_id' => $request->typeID,
                    'refer' => $request->refer,
                    'user_id' => $request->userID
                ]);

                return response()->json([
                    'message' => "Laravel function update post success.",
                    'post' => $post
                ], 201);
            } else {
                return response()->json([
                    'message' => "Laravel function update post false.",
                    'post' => $post
                ], 204);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel controller function update error",
                'error' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->update([
                'deletetion_status' => 'true',
            ]);

            $dateTime = Carbon::now();
            PostDeletetion::updateOrCreate(
                ['post_id' => $post->id],
                [
                    'date_time_delete' => $dateTime,
                    'deletetion_status' => 'true',
                ]
            );

            return response()->json([
                'message' => "Post deleted successfully.",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error during post deletion.",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recoverGetPost(string $userID)
    {
        try {

            $recoverPosts = Post::with('postType')
                ->where('user_id', $userID)
                ->where('deletetion_status', 'true')
                ->where('block_status', 'false')
                ->orderBy('created_at', 'desc')
                ->get();
            // dd($recoverPosts);
            if ($recoverPosts) {
                return response()->json([
                    'message' => "Laravel recoverPosts GET success.",
                    'recoverPosts' => $recoverPosts
                ], 200);
            } else {
                dd($recoverPosts);
            }
        } catch (\Exception $e) {
            return response()->json([
                'messageError' => "Laravel recover prost error" . $e->getMessage()
            ], 400);
        }
    }

    public function recoverPost(string $postID)
    {
        try {

            $statusRecoverPost = Post::where('id', $postID)->first();

            if ($statusRecoverPost) {
                $statusRecoverPost->update([
                    'deletetion_status' => 'false'
                ]);

                if ($statusRecoverPost) {
                    return response()->json([
                        'message' => "laravel recover post success",
                        'post' => $statusRecoverPost
                    ], 201);
                }
            } else {
                dd($statusRecoverPost);
            }
        } catch (\Exception $e) {
            return response()->json([
                'messageError' => "Laravel recover prost error" . $e->getMessage()
            ], 401);
        }
    }

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

            return response()->json([
                'message' => "Pop Like updated successfully.",
                'updatedReactions' => $updatedReactions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error updating Pop Like.",
                'error' => $e->getMessage()
            ], 400);
        }
    }

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

            return response()->json([
                'message' => "Pop Dislike updated successfully.",
                'updatedReactions' => $updatedReactions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error updating Pop Dislike.",
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
