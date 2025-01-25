<?php

namespace App\Http\Controllers;

use App\Models\AuthLogin;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'statusID' => 'required|integer',
            ]);

            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'status_id' => $request->statusID
            ]);

            if ($user) {

                $dateTimeUpdate = Carbon::now('Asia/Bangkok')->setTimezone('UTC');
                UserProfile::create([
                    'user_id' => $user->id,
                    'title_name' => "",
                    'full_name' => "",
                    'nick_name' => "",
                    'updated_at' => $dateTimeUpdate
                ]);

                $token = $user->createToken($user->username);

                $dateTimeLogin = Carbon::now()->format('Y-m-d');
                $user_login = UserLogin::create([
                    'user_id' => $user->id,
                    'user_status_login_number' => 1,
                    'user_status_login_name' => "online",
                    'user_date_time_login' => $dateTimeLogin
                ]);


                if ($user->id && $user_login->user_id) {
                    return response()->json([
                        'user' => $user,
                        'token' => $token->plainTextToken,
                        'message' => 'User registered successfully.',
                        'status' => true
                    ], 201);
                }
            } else {
                return response()->json([
                    'message' => "Laravel register false",
                    'status' => false,
                    'user' => $user
                ]);
            }



        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in register function.',
                'error' => $e->getMessage()
            ], 401);
        }
    }


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

            $dateTimeLogin = Carbon::now()->format('Y-m-d');
            $userLogin = UserLogin::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_status_login_number' => 1,
                    'user_status_login_name' => "online",
                    'user_date_time_login' => $dateTimeLogin
                ]
            );

            if ($userLogin) {
                return response()->json([
                    'message' => "Login successfullry.",
                    'token' => $token,
                    'status' => true,
                    'user' => $user,
                    'user_login' => $userLogin,
                ], 200);
            } else {
                return response()->json([
                    'message' => "Login not success.",
                    'status' => false,
                    'user_login' => $userLogin,
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {

            if ($user = $request->user()) {
                $userLogin = UserLogin::where('user_id', $user->id)->first();
                if ($userLogin) {
                    $dateTimeLogin = Carbon::now()->format('Y-m-d');
                    $userLogin = UserLogin::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'user_status_login_number' => 0,
                            'user_status_login_name' => "offline",
                            'user_date_time_login' => $dateTimeLogin
                        ]
                    );
                }

                $user->tokens()->delete(); // Revoke all tokens for the user

                return response()->json([
                    'message' => 'Logged out successfully.',
                    'status' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Logged out not success.',
                    'status' => false
                ], 400);
            }


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during logout.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
