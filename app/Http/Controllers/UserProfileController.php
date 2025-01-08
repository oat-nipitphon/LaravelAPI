<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user_profile = User::with('user_profile')->get();

            return response()->json([
                'message' => "Laravel api user profile success.",
                'user_profile' => $user_profile
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel profile controller error",
                'error' => $e->getMessage()
            ], 401);
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
    public function show(Request $request, string $id)
    {
        try {
            $user_profiles = User::with('status_user', 'user_profile')->where('id', $id)->first();

            if (!$user_profiles) {
                return response()->json([
                    'message' => "laravel user profiles false"
                ]);
                // dd($userProfile, $id);
            }

            return response()->json([
                'message' => "laravel get user profile function show success.",
                // 'userProfile' => $userProfile,
                'userProfile' => $user_profiles
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel user profile function show error",
                'error' => $e->getMessage(),

            ], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserProfile $userProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserProfile $userProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserProfile $userProfile)
    {
        //
    }
}
