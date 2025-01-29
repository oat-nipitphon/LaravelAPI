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
                'user.userProfile',
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
    public function show(AdminManagerPost $adminManagerPost, string $postID)
    {
        // try {

        //     $post = Post::with('')->where('id', $postID)->first();

        //     if ($post) {
        //         return response()->json([
        //             'message' => "Laravel api get response show success",
        //             'post' => $post
        //         ], 200);
        //     } else {
        //         return response()->json([
        //             'message' => "Laravel api get response show false"
        //         ]);
        //     }

        // } catch (\Exception $error) {
        //     return response()->json([
        //         'message' => "Laravel api function show error". $error->getMessage()
        //     ], 400);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminManagerPost $adminManagerPost, string $postID)
    {
        // try {

        //     $post = Post::with('')->where('id', $postID)->first();

        //     if ($post) {
        //         return response()->json([
        //             'message' => "Laravel api update response success",
        //             'post' => $post
        //         ], 200);
        //     } else {
        //         return response()->json([
        //             'message' => "Laravel api update response false"
        //         ]);
        //     }

        // } catch (\Exception $error) {
        //     return response()->json([
        //         'message' => "Laravel api function update error". $error->getMessage()
        //     ], 400);
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminManagerPost $adminManagerPost, string $postID)
    {
        try {

            $post = Post::findOrFail($postID);

            if ($post) {
                $post->delete();
                return response()->json([
                    'message' => "Laravel api delete success",
                ], 200);
            } else {
                return response()->json([
                    'message' => "Laravel api delete false"
                ], 201);
            }

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel api function destroy error". $error->getMessage()
            ], 400);
        }
    }

    public function blockOrUnblockPost (string $postID, string $blockStatus) {
        try {

            $post = Post::findOrFail($postID);

            if ($post) {

                if ($blockStatus === "true") {
                    $confirm = $post->updateOrCreate([
                        ['id' => $post->id],
                        [
                            'block_status' => "true"
                        ]
                    ]);
                }

                if ($blockStatus === "false") {
                    $confirm = $post->updateOrCreate([
                        ['id' => $post->id],
                        [
                            'block_status' => "false"
                        ]
                    ]);
                }

                if (!$confirm) {
                    dd("error update or create block status", $confirm);
                }

                return response()->json([
                    'message' => "Laravel api block post success",
                    'post' => $post
                ], 200);

            } else {
                return response()->json([
                    'message' => "Laravel api block post false"
                ], 201);
            }

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel api function blockOrUnblockPost error". $error->getMessage()
            ], 400);
        }
    }

}
