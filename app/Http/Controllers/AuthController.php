<?php

namespace App\Http\Controllers;

// use App\Models\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register (Request $request) {
        try {

            // $fields = $request->validate([
            //     'name' => 'required|max:255',
            //     'email' => 'required|email|unique:users',
            //     'password' => 'required|confirmed',
            //     'status' => 'required'
            // ]);

            // $user = User::create($fields);

            $request->validate([
                // 'name' => 'required|string|max:255',
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

            $token = $user->createToken($request->username);

            if (isset($token)) {
                return response()->json([
                    'user' => $user,
                    'token' => $token->plainTextToken,
                    'message' => 'User registered successfully.'
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in register function.',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function forgetYourPassword (Request $request) {
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

    public function login (Request $request) {
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
                    'message' => "laravel login false.",
                    'user' => $user,
                    'req' => $request->all()
                ]);
            }
            $token = $user->createToken($user->username);

            // if (!Auth::attempt($request->only('email','username','password'))) {
            //     return response()->json([
            //         'message' => "Invalid credentials",
            //     ], 401);
            // }
            // $user = $request->user();
            // $token = $request->user()->createToken($request->username);

            if (isset($token)) {
                return response()->json([
                    'message' => "login success.",
                    'token' => $token->plainTextToken,
                    'user' => $user
                ], 200);
            } else {
                dd($user, $token);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in login function.',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function logout (Request $request) {
        try {

            // $request->user()->currentAccessToken->delete();
            $request->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in logout function.',
                'error' => $e->getMessage()
            ], 401);
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
