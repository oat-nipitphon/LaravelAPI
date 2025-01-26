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

            // status 0=false 1=true
            $posts = Post::with(
                'postImage',
                'postType',
                'user',
                'postPopularity',
                'user.userProfile.userProfileImage',
            )
                ->where('deletetion_status', 0)
                ->orderBy('created_at', 'desc')
                ->get();



            return response()->json([
                'message' => "Laravel api get posts success.",
                'posts' => $posts

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

            $dateTimeCreatePost = Carbon::now('Asia/Bangkok')->setTimezone('UTC');

            $post = Post::create([
                'post_title' => $validated['title'],
                'post_content' => $validated['content'],
                'refer' => $validated['refer'],
                'type_id' => $validated['typeID'],
                'user_id' => $validated['userID'],
                'deletetion_status' => 0, // status 0 == false // status 1 == true
                'created_at' => $dateTimeCreatePost,
            ]);

            if (!$post) {
                return response()->json([
                    'message' => 'Failed to create the post. Please try again.',
                ], 500);
            }

            if ($request->hasFile('image')) {

                // Move file image
                $validated['image'] = $request->file('image')->store('images', 'public');

                $postImage = PostImage::create([
                    'post_id' => $post->id,
                    'image_name' => $validated['image']
                ]);

                if (!$postImage) {
                    return response()->json([
                        'message' => 'Failed to save the image. Post created without image.',
                        'post' => $post
                    ], 206);
                }
            }

            return response()->json([
                'message' => 'Post created successfully!',
                'post' => $post->load('postImage') // load model Relationships
            ], 201);

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
                'deletetion_status' => 1,
            ]);

            $dateTime = Carbon::now();
            PostDeletetion::updateOrCreate(
                ['post_id' => $post->id],
                [
                    'date_time_delete' => $dateTime,
                    'deletetion_status' => 1,
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
                ->where('deletetion_status', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            if (!$recoverPosts) {
                dd($recoverPosts);
            }

            return response()->json([
                'message' => "Laravel recoverPosts GET success.",
                'recoverPosts' => $recoverPosts
            ], 200);
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
                    'deletetion_status' => 0
                ]);

                if ($statusRecoverPost) {
                    return response()->json([
                        'message' => "laravel recover post success",
                        'post' => $statusRecoverPost
                    ], 201);
                }
            }

            dd($statusRecoverPost);
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
