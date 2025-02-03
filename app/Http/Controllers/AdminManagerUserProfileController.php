<?php

namespace App\Http\Controllers;

use App\Models\AdminManagerUserProfile;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class AdminManagerUserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userProfiles = UserProfile::with([
                'userProfileImage',
                'user',
                'user.userProfileContact',
                'user.statusUser',
                'user.posts',
                'user.userFollowersProfile',
                'user.userFollowersAccount',
                'user.userLogin',
                'user.latestUserLogin',
            ])
                ->get()
                ->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'full_name' => $profile->full_name,
                        'title_name' => $profile->title_name,
                        'user' => [
                            'id' => $profile->user->id,
                            'name' => $profile->user->name,
                            'email' => $profile->user->email,
                            'status_user' => $profile->user->statusUser->status_name ?? null,
                            'posts' => $profile->user->posts, // เอาทั้งหมด
                            'latest_post' => $profile->user->posts->sortByDesc('created_at')->first(), // เอาโพสต์ล่าสุด
                            'user_login' => $profile->user->userLogin->sortByDesc('created_at')->first() ?? null, // เอาข้อมูล login ล่าสุด
                        ],
                        'user_contact' => $profile->user->userProfileContact->map(function ($contact) {
                            return [
                                'id' => $contact->id,
                                'contact_name' => $contact->contact_name,
                                'contact_link_path' => $contact->contact_link_path,
                                'contact_icon_name' => $contact->contact_icon_name,
                                'contact_icon_url' => $contact->contact_icon_url,
                                'contact_icon_data' => $contact->contact_icon_data ? 'data:image/png;base64,'
                                . base64_encode($contact->contact_icon_data) : null,
                            ];
                        }),
                    ];
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
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel admin manager user profile error.",
                'error' => $e->getMessage()
            ]);
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
