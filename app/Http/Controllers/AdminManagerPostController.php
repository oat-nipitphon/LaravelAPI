<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminManagerPost;
use App\Models\Post;
use App\Models\PostDeletetion;
use App\Models\PostImage;
use App\Models\PostPopularity;
use App\Models\PostComment;
use App\Models\PostCommentPopularity;
use Illuminate\Support\Facades\DB;

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
        try {

            $post = Post::with(['user', 'postImages', 'postType', 'postDeletetions', 'postPopularities', 'postComments'])
                ->findOrFail($postID);

            return response()->json($post);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel api function show error" . $error->getMessage()
            ], 400);
        }
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

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack(); // ย้อนกลับการเปลี่ยนแปลงหากมีข้อผิดพลาดเกี่ยวกับ Database

            return response()->json([
                'message' => "Database error: " . $e->getMessage()
            ], 500);
        } catch (\Exception $error) {
            DB::rollBack(); // ย้อนกลับการเปลี่ยนแปลงหากมีข้อผิดพลาดอื่น ๆ

            return response()->json([
                'message' => "Laravel API function destroy error: " . $error->getMessage()
            ], 400);
        }
    }

    public function blockOrUnblockPost(string $postID, string $blockStatus)
    {
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
                'message' => "Laravel api function blockOrUnblockPost error" . $error->getMessage()
            ], 400);
        }
    }
}
