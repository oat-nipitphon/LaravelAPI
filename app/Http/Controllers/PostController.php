<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with('post_types', 'user')->get();

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

            $post = new Post();

            $post->create([
                'post_title' => $request->title,
                'post_content' => $request->content,
                'refer' => $request->refer,
                'type_id' => $request->typeID,
                'user_id' => $request->userID
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

            $post = Post::FindOrFail($id);

            if (!$post) {
                return response()->json([
                    'message' => "Laravel destroy request false.",
                    'post' => $post,
                    'id' => $id
                ], 204);
            }

            $post->delete();

            return response()->json([
                'message' => "Laravel destroy post success."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel destroy error",
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
