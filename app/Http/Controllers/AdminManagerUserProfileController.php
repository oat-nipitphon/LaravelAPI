<?php

namespace App\Http\Controllers;

use App\Models\AdminManagerUserProfile;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;

class AdminManagerUserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user = User::with(
                'userProfileContact',
                'userProfileImage',
                'userLogin',
                'statusUser',
                'posts',
                'userFollowersProfile',
                'userFollowersAccount'
            )->get();

            dd($user);
            $userProfiles = [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'name' => $user->name ?? null,
                'username' => $user->username ?? null,
                'statusUser' => $user->statusUser ?[
                    'id' => $user->statusUser->id ?? null,
                    'status_name' => $user->statusUser->status_name ?? null,
                ] : null,
                'userProfile' => $user->userProfile ? [
                    'id' => $user->userProfile->id ?? null,
                    'title_name' => $user->userProfile->title_name ?? null,
                    'full_name' => $user->userProfile->full_name ?? null,
                    'nick_name' => $user->userProfile->nick_name ?? null,
                    'tel_phone' => $user->userProfile->tel_phone ?? null,
                    'birth_day' => $user->userProfile->birth_day ?? null,
                ] : null,
                'userProfileContact' => $user->userProfileContact?->map(function ($contact) {
                    return [
                        'id' => $contact->id ?? null,
                        'contact_name' => $contact->contact_name ?? null,
                        'contact_link_path' => $contact->contact_link_path ?? null,
                        'contact_icon_name' => $contact->contact_icon_name ?? null,
                        'contact_icon_url' => $contact->contact_icon_url ?? null,
                        'contact_icon_data' => $contact->contact_icon_data ? 'data:image/png;base64,'
                        . base64_encode($contact->contact_icon_data) : null ?? null,
                    ];
                }) ?? null,
                'userProfileImage' => $user->userProfile->userProfileImage?->map(function ($profileImage) {
                    return [
                        'id' => $profileImage->id ?? null,
                        'imagePath' => $profileImage->image_path ?? null,
                        'imageName' => $profileImage->image_name ?? null,
                        'imageData' => $profileImage->image_data ?? null,
                    ];
                }) ?? null,
                'userLogin' => $user->userLogin ? [
                    'id' => $user->userLogin->id ?? null,
                    'statusLogin' => $user->userLogin->status_login ?? null,
                    'createdAt' => $user->userLogin->created_at ?? null,
                    'updatedAt' => $user->userLogin->updated_at ?? null,
                ] : null,
            ];

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
    public function show(AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
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
