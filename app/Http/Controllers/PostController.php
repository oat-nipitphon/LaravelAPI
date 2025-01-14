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

            $posts = Post::with('post_types','user')->get();

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
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post, string $id)
    {
        try {

            // $posts = Post::with('post_types','user')->get();

            return response()->json([
                'message' => "Laravel api get posts success.",
                'post' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel api get posts error",
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
