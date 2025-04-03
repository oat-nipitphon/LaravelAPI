<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserProfile;
use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{


    public function getStatusUser()
    {
        $userStatus = UserStatus::all();
        return response()->json([
            'userStatus' => $userStatus,
        ], 200);
    }

    // Register
    public function register(Request $request)
    {
        try {
            $validate = $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:3',
                'statusID' => 'required|integer',
            ]);


            $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            $user = User::create([
                'name' => $validate['username'],
                'username' => $validate['username'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'status_id' => $validate['statusID'],
                'created_at' => $dateTimeNow
            ]);

            if (empty($user)) {
                return response()->json([
                    'message' => 'api register user false'
                ]);
            }

            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'created_at' => $dateTimeNow
            ]);

            if (empty($userProfile)) {
                return response()->json([
                    'message' => 'api register user profile false'
                ]);
            }

            return response()->json([
                'message' => 'api register success',
                'user' => $user,
                'userProfile' => $userProfile
            ], 201);


            // Register Success Login Yes ?
            // $token = $user->createToken($validate['username']);
            // if (isset($token)) {
            //     return response()->json([
            //         'message' => 'api register success',
            //         'user' => $user,
            //         'profile' => $userProfile,
            //         'token' => $token->pla
            //     ], 201);
            // }

        } catch (\Exception $error) {
            return response()->json([
                'message' => "backend api function register error -> " . $error->getMessage()
            ], 500);
        }
    }

    // Reset password
    public function forgetYourPassword(Request $request)
    {
        try {

            $request->validate([
                'emailUsername' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->emailUsername)
                ->orWhere('username', $request->emailUsername)
                ->first();

            if ($user) {

                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                $token = $user->createToken($user->username);

                return response()->json([
                    'message' => "Laravel forget your password function success.",
                    'user' => $user,
                    'token' => $token->plainTextToken
                ], 201);
            }

            return response()->json([
                'message' => "Laravel user false",
                'req' => $request->all()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel forget your password function error",
                'error' => $e->getMessage()
            ], 401);
        }
    }

    // Login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'emailUsername' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->emailUsername)
                ->orWhere('username', $request->emailUsername)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => "Invalid credentials.",
                ], 401);
            }

            $token = $user->createToken($user->username)->plainTextToken;

            if ($user && $token) {

                $dateTimeNow = Carbon::now('Asia/Bangkok')->format("Y-m-d H:i:s");

                $userLogin = UserLogin::create([
                    'user_id' => $user->id,
                    'status_login' => "online",
                    'created_at' => $dateTimeNow,
                ]);

                if (!$userLogin) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Login not success.",
                        'status' => false,
                        'user_login' => $userLogin,
                    ], 400);
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Login successfullry.",
                    'token' => $token,
                    'user' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        try {


            if ($user = $request->user()) {

                $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');


                $userLogout = UserLogin::create([
                    'user_id' => $user->id,
                    'status_login' => "offline",
                    'updated_at' => $dateTimeNow
                ]);


                if (!$userLogout) {
                    return response()->json([
                        'status' => 204,
                        'message' => "Laravel function logout request user false",
                        'requestUser' => $request->user()
                    ]);
                }

                $user->tokens()->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "Laravel function logout successfullry",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel function logout error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * Get user auth login
     */
    public function index(Request $request)
    {
        try {
            $user_req = $request->user();
            $user_login = User::with([

                'userImage',
                'userProfile',
                'userProfileImage',
                'userProfileContact',
                'latestUserLogin',
                // 'userPoint',
                // 'userPoint.userPointCounter'

            ])->findOrFail($user_req->id);

            $token = $user_login->createToken($user_login->username)->plainTextToken;

            $user_login = [
                'id' => $user_login->id,
                'name' => $user_login->name,
                'email' => $user_login->email,
                'username' => $user_login->username,
                'status_id' => $user_login->status_id,
                'created_at' => $user_login->created_at,
                'updated_at' => $user_login->updated_at,

                'userStatus' => $user_login->userStatus ? [
                    'id' => $user_login->userStatus->id,
                    'status_code' => $user_login->userStatus->status_code,
                    'status_name' => $user_login->userStatus->status_name,
                ] : null,

                'userLogin' => $user_login->latestUserLogin ? [
                    'id' => $user_login->latestUserLogin->id,
                    'user_id' => $user_login->latestUserLogin->user_id,
                    'status_login' => $user_login->latestUserLogin->status_login,
                    'created_at' => $user_login->latestUserLogin->created_at,
                    'updated_at' => $user_login->latestUserLogin->updated_at,
                    'total_time_login' => $user_login->latestUserLogin->total_time_login,
                ] : null,

                'userProfile' => $user_login->userProfile ? [
                    'id' => $user_login->userProfile->id,
                    'user_id' => $user_login->userProfile->user_id,
                    'title_name' => $user_login->userProfile->title_name,
                    'full_name' => $user_login->userProfile->full_name,
                    'nick_name' => $user_login->userProfile->nick_name,
                    'tel_phone' => $user_login->userProfile->tel_name,
                    'birth_day' => $user_login->userProfile->birth_day,
                    'created_at' => $user_login->userProfile->created_at,
                    'updated_at' => $user_login->userProfile->updated_at,
                ] : null,

                'userImage' => $user_login->userImage->map(function ($userImage) {
                    return [
                        'id' => $userImage->id,
                        'imageData' => $userImage->image_data,
                    ];
                }),



                // 'userPoint' => $user_login->userPoint ? [
                //     'id' => $user_login->userPoint->id,
                //     'user_id' => $user_login->userPoint->user_id,
                //     'point' => $user_login->userPoint->point,
                //     'created_at' => $user_login->userPoint->created_at,
                //     'updated_at' => $user_login->userPoint->updated_at,
                // ] : null,

                // 'userPointCounter' => $user_login->userPoint && $user_login->userPoint->userPointCounter
                //     ? $user_login->userPoint->userPointCounter->map(function ($counter) {
                //         return $counter ? [
                //             'id' => $counter->id,
                //             'user_id' => $counter->user_id,
                //             'reward_id' => $counter->reward_id,
                //             'point_import' => $counter->point_import,
                //             'point_export' => $counter->point_export,
                //             'detail_counter' => $counter->detail_counter,
                //             'created_at' => $counter->created_at,
                //             'updated_at' => $counter->updated_at,
                //         ] : null;
                //     }) : null,

            ];

            // dd($user_login);

            return response()->json([
                'user_login' => $user_login,
                'token' => $token
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel auth controller function error " . $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Auth $auth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auth $auth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auth $auth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auth $auth)
    {
        //
    }
}
