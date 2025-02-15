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
                'statusUser',
                'latestUserLogin',
                'userProfiles',
                'userProfileImage',
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
                    ] : [
                        'id' => "null",
                        'user_id' => "null",
                        'status_login' => "null",
                        'created_at' => "null",
                        'updated_at' => "null",
                        'total_time_login' => "null",
                    ],
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
                    'userProfileImage' => $user->userProfileImage->map(function ($image) {
                        return $image ? [
                            'id' => $image->id,
                            'user_id' => $image->user_id,
                            'image_name' => $image->image_name,
                            'image_path' => $image->image_path,
                            'image_url' => $image->image_url,
                            'image_data' => 'data:image/png;base64,' . base64_encode($image->image_data),
                            'created_at' => $image->created_at,
                            'updated_at' => $image->updated_at,
                        ] : null;
                    }),
                    'userContact' => $user->userProfileContact->map(function ($contact) {
                        return $contact ? [
                            'id' => $contact->id,
                            'user_id' => $contact->user_id,
                            'contact_name' => $contact->contact_name,
                            'contact_link_address' => $contact->contact_link_address,
                            'contact_link_path' => $contact->contact_link_path,
                            'contact_icon_name' => $contact->contact_icon_name,
                            'contact_icon_url' => $contact->contact_icon_url,
                            'contact_icon_data' => 'data:image/png;base64,' . base64_encode($contact->contact_icon_data),
                            'created_at' => $contact->created_at,
                            'updated_at' => $contact->updated_at,
                        ] : null;
                    }),

                ] : null;
            });

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

            $userProfile = UserProfile::findOrFail($userProfileID);

            if ($userProfile) {
                return response()->json([
                    'message' => "laravel function show get userprofile success.",
                    'userProfile' => $userProfile,
                    'status' => 200,
                ], 200);
            }
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
