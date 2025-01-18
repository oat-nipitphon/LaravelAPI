<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostDeletetion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Faker\Core\DateTime;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            // status 0=false 1=true
            $posts = Post::with('post_types', 'user')
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
            $request->validate([
                'userID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'required|string',
                'typeID' => 'required|integer',
            ]);

            $dateTimeCreatePost = Carbon::now('Asia/Bangkok')->setTimezone('UTC');
            $post = new Post();
            $post->create([
                'post_title' => $request->title,
                'post_content' => $request->content,
                'refer' => $request->refer,
                'type_id' => $request->typeID,
                'user_id' => $request->userID,
                'deletetion_status' => 0, // status 0 == false // status 1 == true
                'created_at' => $dateTimeCreatePost,
            ]);

            if (!$post) {
                dd($request, $post);
            }

            return response()->json([
                'message' => "laravel function store success.",
                'createPostNew' => $post
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel function store error :",
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

            $post = Post::with('post_types', 'user')->findOrFail($id);

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

            $recoverPosts = Post::with('post_types')
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
}
