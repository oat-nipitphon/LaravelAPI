<?php

namespace App\Http\Controllers;

use App\Models\AdminManagerUserProfile;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use Illuminate\Http\Request;

class AdminManagerUserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userProfiles = User::with(
                'userImage',
                'statusUser',
                'latestUserLogin',
                'userProfiles',
                'userProfileContact'
            )->get()->map(function ($user) {
                return $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'status_id' => $user->status_id,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'userLogin' => $user->latestUserLogin ? [
                        'id' => $user->latestUserLogin->id,
                        'user_id' => $user->latestUserLogin->user_id,
                        'status_login' => $user->latestUserLogin->status_login,
                        'created_at' => $user->latestUserLogin->created_at,
                        'updated_at' => $user->latestUserLogin->updated_at,
                        'total_time_login' => $user->latestUserLogin->total_time_login,
                    ] : null,
                    'userImage' => $user->userImage->map(function ($userImage) {
                        return $userImage ? [
                            'id' => $userImage->id,
                            'user_id' => $userImage->user_id,
                            'imageData' => $userImage->image_data,
                            'created_at' => $userImage->created_at,
                            'updated_at' => $userImage->updated_at,
                        ] : null;
                    }),
                    'userProfile' => $user->userProfiles->map(function ($profile) {
                        return $profile ? [
                            'id' => $profile->id,
                            'user_id' => $profile->user_id,
                            'title_name' => $profile->title_name,
                            'full_name' => $profile->full_name,
                            'nick_name' => $profile->nick_name,
                            'tel_phone' => $profile->tel_phone,
                            'birth_day' => $profile->birth_day,
                            'created_at' => $profile->created_at,
                            'updated_at' => $profile->updated_at,
                        ] : null;
                    }),


                ] : null;
            });

            dd($userProfiles);

            if ($userProfiles) {
                return response()->json([
                    'message' => "Laravel get user profile detail success",
                    'userProfiles' => $userProfiles
                ], 200);
            }

            return response()->json([
                'message' => "Laravel admin manager user profile false.",
                'userProfiles' => $userProfiles
            ], 204);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel admin manager user profile error.",
                'error' => $e->getMessage()
            ], 500);
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
    public function show(AdminManagerUserProfile $adminManagerUserProfile, string $userProfileID)
    {
        try {

            $userProfile = UserProfile::with(
                'user',
                'user.userProfileImage'
            )->findOrFail($userProfileID);


        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel controller function show error" . $error->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
    }
}
