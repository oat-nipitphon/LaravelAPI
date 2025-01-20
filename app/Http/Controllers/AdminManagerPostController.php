<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminManagerPost;
use App\Models\Post;

class AdminManagerPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with(
                'user',
                'postType',
                'postDeletetion',
                'postPopularity',
                'postComment'
                )
            ->get();

            if (!$posts) {
                return [
                    'message' => "laravel admin post index false",
                    'posts' => $posts,
                    400
                ];
            } else {
                return [
                    'message' => "Laravel admin post index success",
                    'posts' => $posts,
                    200
                ];
            }

        } catch (\Exception $e) {
            return [
                'message' => "Laravel admin api manager post error" .
                $e->getMessage(),
                500
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminManagerPost $adminManagerPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminManagerPost $adminManagerPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminManagerPost $adminManagerPost)
    {
        //
    }
}
