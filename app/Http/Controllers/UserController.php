<?php

namespace App\Http\Controllers;

use App\Models\FollowersProfile;
use App\Models\PopularityProfile;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'message' => "get user success.",
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel function index user controller error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function followersProfile(Request $request, string $postUserID, string $authUserID)
    {
        try {
            $checkFollowers = FollowersProfile::where('profile_user_id', $postUserID)
                ->where('followers_user_id', $authUserID)
                ->first();
            $check = '';
            if ($checkFollowers) {
                if ($checkFollowers->status_followers === 'true') {
                    $checkFollowers->delete();
                    $check = 'false';
                } else {
                    $checkFollowers->update([
                        'status_followers' => 'true',
                        'updated_at' => now(),
                    ]);
                    $check = 'true';
                }
            } else {
                FollowersProfile::create([
                    'profile_user_id' => $postUserID,
                    'followers_user_id' => $authUserID,
                    'status_followers' => 'true',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $check = 'true';
            }

            return response()->json([
                'message' => 'laravel api followers profile success.',
                'checkFollowers' => $check
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel followersProfile function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function popLikeProfile(Request $request, string $postUserID, string $authUserID)
    {
        try {

            $checkPopLike = PopularityProfile::where('user_id', $postUserID)
                ->where('user_id_pop', $authUserID)->first();
            $check = '';

            if ($checkPopLike) {
                if ($checkPopLike->status_pop === 'true') {
                    $checkPopLike->delete();
                    $check = 'false';
                } else {
                    $checkPopLike->update([
                        'status_pop' => 'true',
                        'updated_at' => now(),
                    ]);
                    $check = 'true';
                }
            } else {
                PopularityProfile::create([
                    'user_id' => $postUserID,
                    'user_id_pop' => $authUserID,
                    'status_pop' => 'true',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $check = 'true';
            }

            return response()->json([
                'message' => 'laravel api pop like profile success.',
                'checkPopLike' => $check
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel popLikeProfile function error",
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

            $request->validate([
                'userID' => 'required|integer',
                'name' => 'required|string',
                'email' => 'required|string',
                'userName' => 'required|string',
                'password' => 'required|string', //Hash::make($request->password)
                'statusID' => 'required|integer',
            ]);

            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            $user = User::findOrFail($request->userID);

            if ($user) {

                $user->update([
                    'name' => $request->userID,
                    'email' => $request->email,
                    'username' => $request->userName,
                    'password' => Hash::make($request->password),
                    'status_id' => $request->statusID,
                    'updated_at' => $dateTime,
                ]);

                return response()->json([
                    'message' => "update user success.",
                    'user' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => "update user false success.",
                    'request' => $request->all()
                ], 204);
            }
        } catch (\Exception $error) {
            return response()->json([
                'VueLaravelAPI' => "apiUpdateUser -> user controller function store",
                'message_error' => "function error" . $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function updateUser(Request $request)
    {
        try {

            $request->validate([
                'userID' => 'required|integer|exists:users,id',
                'email' => 'nullable|string|email',
                'username' => 'nullable|string',
                'name' => 'nullable|string',
                'statusID' => 'nullable|integer',
            ]);

            $user = User::findOrFail($request->userID);

            if (!$user) {
                return response()->json([
                    'message' => "api update user not success",
                    'user' => $request->all()
                ], 404);
            }
            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            $user->update(array_filter([
                'email' => $request->email,
                'username' => $request->username,
                'name' => $request->username,
                'status_id' => $request->statusID,
                'updated_at' => $dateTime
            ]));

            return response()->json([
                'message' => "api update user successfully",
                'user' => $user
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "api user controller function update user error" . $error->getMessage()
            ]);
        }
    }

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
