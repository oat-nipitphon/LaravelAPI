<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facedes\Validator;
use Intervention\Image\Facedes\Image;


class UserProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userProfiles = User::with(
                'userProfileContact',
                'userProfile.userProfileImage',
                'userLogin',
                'statusUser',
                'posts',
                'userFollowersProfile',
                'userFollowersAccount'

            )->get();

            return response()->json([
                'message' => "Laravel api user profile success.",
                'user_profile' => $userProfiles
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
        try {

            $validated = $request->validate([
                'profileID' => 'required|integer',
                'titleName' => 'required|string',
                'fullName' => 'required|string',
                'nickName' => 'required|string',
                'telPhone' => 'required|string',
                'birthDay' => 'required|date',

            ]);

            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');
            $userProfile = UserProfile::findOrFail($validated['profileID']);

            if ($userProfile) {

                $userProfile->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTime,
                ]);

                return response()->json([
                    'message' => 'update user profile success',
                    'userProfile' => $userProfile,
                    'status' => true
                ], 200);
            }

            return response()->json([
                'message' => "update user profile false",
                'status' => false
            ], 400);
        } catch (\Exception $error) {
            return response()->json([
                'VueLaravelAPI' => "store apiUpdateDetailUserProfile -> controller function store",
                'message' => 'function error :: ',
                'error' => $error->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {

            $userProfile = User::with(
                'userProfile',
                'userProfileContact',
                'userProfileImage',
                'userLogin',
                'statusUser',
                'posts',
                'userFollowersProfile',
                'userFollowersAccount'
            )->findOrFail($id);

            $userProfiles = [
                'id' => $userProfile->id,
                'email' => $userProfile->email,
                'name' => $userProfile->name,
                'username' => $userProfile->username,

                'statusUser' => $userProfile->statusUser ? [
                    'id' => $userProfile->statusUser->id,
                    'status_name' => $userProfile->statusUser->status_name,
                ] : null,

                'userProfile' => $userProfile->userProfile ? [
                    'id' => $userProfile->userProfile->id,
                    'title_name' => $userProfile->userProfile->title_name,
                    'full_name' => $userProfile->userProfile->full_name,
                    'nick_name' => $userProfile->userProfile->nick_name,
                    'tel_phone' => $userProfile->userProfile->tel_phone,
                    'birth_day' => $userProfile->userProfile->birth_day,
                ] : null,

                'userProfileImage' => $userProfile->userProfileImage->map(function ($image) {
                    return $image ? [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                        'image_name' => $image->image_name,
                        'image_data' => $image->image_data ? 'data:image/png;base64,' . base64_encode($image->image_data) : null,
                    ] : null;
                }),

                'userProfileContact' => $userProfile->userProfileContact->map(function ($contact) {
                    return $contact ? [
                        'id' => $contact->id,
                        'contact_name' => $contact->contact_name,
                        'contact_link_path' => $contact->contact_link_path,
                        'contact_icon_name' => $contact->contact_icon_name,
                        'contact_icon_url' => $contact->contact_icon_url,
                        'contact_icon_data' => $contact->contact_icon_data ? 'data:image/png;base64,'
                            . base64_encode($contact->contact_icon_data) : null,
                    ] : null;
                }),

            ];

            if ($userProfiles) {
                return response()->json([
                    'message' => "Laravel get user profile detail success",
                    'userProfiles' => $userProfiles
                ], 200);
            }

            return response()->json([
                'message' => "laravel get user profile not success.",
                'userProfiles' => $userProfiles
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel user profile function show error",
                'error' => $e->getMessage(),
            ], 500);
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
        try {
            // ใช้ validate() ในการตรวจสอบข้อมูลจาก request
            $validated = $request->validate([
                'userID' => 'required|integer',
                'name' => 'required|string',
                'email' => 'required|string',
                'userName' => 'required|string',
                'statusID' => 'required|integer',
                'profileID' => 'required|integer',
                'titleName' => 'required|string',
                'fullName' => 'required|string',
                'nickName' => 'required|string',
                'telPhone' => 'required|string',
                'birthDay' => 'required|date',
            ]);

            // ส่วนการจัดการข้อมูลต่อไป (เช่น การอัพเดทข้อมูลในฐานข้อมูล)
            $userProfile = UserProfile::findOrFail($validated['profileID']);
            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            if ($userProfile) {

                // ใช้ข้อมูลที่ผ่านการ validate มาอัพเดท
                $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');
                $userProfile->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTime
                ]);

                $user = User::findOrFail('id', $userProfile->user_id);

                if ($user) {
                    $user->update([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'username' => $validated['userName'],
                        'status_id' => $validated['statusID'],
                    ]);

                    return response()->json([
                        'message' => 'Profile updated successfully.',
                        'userProfile' => $userProfile,
                        'status' => true
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Profile update not success.",
                        'status' => false
                    ]);
                }
            }

            return response()->json([
                'message' => 'User profile not found.'
            ], 404);
        } catch (\Exception $error) {
            // การจัดการข้อผิดพลาด
            return response()->json([
                'message' => 'Laravel api function error :: ',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserProfile $userProfile)
    {
        //
    }
}
