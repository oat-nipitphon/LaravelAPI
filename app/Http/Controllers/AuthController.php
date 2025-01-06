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

            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'status_id' => 'required|string|max:5',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'status_id' => $request->status_id
            ]);

            if (isset($user)) {
                return response()->json([
                    'user' => $user,
                    'message' => 'User registered successfully.'
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in register function.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login (Request $request) {
        try {

            $request->validate([
                'email' => 'required|string|email',
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email','username','password'))) {
                return response()->json([
                    'message' => "Invalid credentials",
                ], 401);
            }

            $user = $request->user();
            // $token = $request->user()->createToken($request->username);
            $token = $user->createToken($request->username);

            return response()->json([
                'message' => "login success.",
                'token' => $token->plainTextToken,
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Laravel Auth Controller error in login function.',
                'error' => $e->getMessage()
            ]);
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
            ]);
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
