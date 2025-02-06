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
            $validate = $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'statusID' => 'required|integer',
            ]);

            $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            $user = User::create([
                'name' => $validate['username'],
                'username' => $validate['username'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'status_id' =>$validate['statusID'],
                'created_at' => $dateTimeNow
            ]);

            $userProfile = UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'title_name' => "",
                    'full_name' => "",
                ],
            );

            $user_login = UserLogin::create([
                'user_id' => $user->id,
                'status_login' => "online",
                'created_at' => $dateTimeNow,
            ]);

            $token = $user->createToken($user->username)->plainTextToken;

            if ($user && $userProfile && $user_login && $token) {

                return response()->json([
                    'message' => "register success",
                    'token' => $token,
                    'user' => $user,
                    'userProfile' => $userProfile,
                    'user_login' => $user_login,
                ], 200);
            }

            return response()->json([
                'message' => "register required false",
                'token' => false,
                'user' => false,
                'userProfile' => false,
                'user_login' => false,
            ], 204);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "backend api function register error -> ". $error->getMessage()
            ], 500);
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


            if ($user) {

                $dateTimeNow = Carbon::now('Asia/Bangkok');
                $userLogin = UserLogin::create([
                    'user_id' => $user->id,
                    'status_login' => "online",
                    'created_at' => $dateTimeNow,
                    'updated_at' => $dateTimeNow,
                ]);

                if ($userLogin) {

                    return response()->json([
                        'message' => "Login successfullry.",
                        'token' => $token,
                        'status' => true,
                        'user' => $user,
                        'user_login' => $userLogin,

                    ], 200);
                }
            }

            return response()->json([
                'message' => "Login not success.",
                'status' => false,
                'user_login' => $userLogin,
            ], 400);
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


                if ($user) {

                    $userLogin = UserLogin::where('user_id', $user->id)->first();

                    $dateTimeNow = Carbon::now('Asia/Bangkok');

                    if ($userLogin) {

                        $userLogin->update([
                            'status_login' => "offline",
                            'updated_at' => $dateTimeNow
                        ]);

                        $user->tokens()->delete();

                        return response()->json([
                            'message' => "Login successfullry.",
                            'status' => true,
                            'user' => $user,
                            'user_login' => $userLogin,

                        ], 200);
                    }
                }
            }

            return response()->json([
                'message' => "Login not success.",
                'status' => false,
                'user_login' => $userLogin,
            ], 400);
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
