<?php

namespace App\Http\Controllers;

use App\Models\AuthLogin;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use App\Models\UserProfileContact;
use App\Models\UserFollowersAccount;
use App\Models\UserFollowersProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{

    // Register
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
                'status_id' => $validate['statusID'],
                'created_at' => $dateTimeNow
            ]);

            $userProfile = UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'title_name' => "",
                    'full_name' => "",
                    'created_at' => $dateTimeNow,
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

            if ($user) {

                $dateTimeNow = Carbon::now('Asia/Bangkok');
                $userLogin = UserLogin::create([
                    'user_id' => $user->id,
                    'status_login' => "online",
                    'created_at' => $dateTimeNow,
                ]);

                if ($userLogin) {

                    return response()->json([
                        'status' => 200,
                        'message' => "Login successfullry.",
                        'token' => $token,
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

    // Logout
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $userLogout = UserLogin::where('user_id', $user->id)->first();
            if (!empty($userLogout)) {
                $dateTimeNow = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

                $userLogout->update([
                    'status_login' => "offline",
                    'updated_at' => $dateTimeNow
                ]);

                $user->tokens()->delete();

                return response()->json([
                    'status' => 200,
                    'message' => "Laravel function logout successfullry",
                ], 200);
            } else {
                return response()->json([
                    'status' => 204,
                    'message' => "Laravel function logout request user false",
                    'requestUser' => $request->user()
                ]);
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
                'userProfile',
                'userProfileImage',
                'userProfileContact'
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
                'userProfileImage' => $user_login->userProfileImage->map(function ($image) {
                    return $image ? [
                        'id' => $image->id,
                        'imageData' => $image->image_data ? 'data:image/png;base64,' . base64_encode($image->image_data) : null,
                    ] : null;
                }),
                'userProfileContact' => $user_login->userProfileContact ?
                    $user_login->userProfileContact->map(function ($contact) {
                        return $contact ? [
                            'id' =>  $contact->id,
                            'userID' =>  $contact->user_id,
                            'name' =>  $contact->contact_name,
                            'linkAdress' => $contact->contact_link_address,
                            'contactPath' =>  $contact->contact_link_path,
                            'contactName' =>  $contact->contact_icon_name,
                            'contactUrl' =>  $contact->contact_icon_url,
                            'contactData' =>  $contact->contact_icon_data ? 'data:image/png;base64,'
                                . base64_encode($contact->contact_icon_data)
                                : null,
                            'createdAt' =>  $contact->created_at,
                            'updatedAt' =>  $contact->updated_at,
                        ] : null;
                    }) : null,
            ];

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
