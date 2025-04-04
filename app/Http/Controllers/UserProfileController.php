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
                'userLogin',
                'userStatus',
                'userImage',
                'userProfile',
                'userProfileContact',
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

            $user = User::with(
                'userImage',
                'userProfile',
                'userProfile.ProfileContact',
                'userProfileContact',
                'userProfileImage',
                'userLogin',
                'userStatus',
                'posts',
                'userPoint',
                // 'userPoint.userPointCounter',
            )->findOrFail($id);

            $userProfile = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'username' => $user->username,
                'statusID' => $user->status_id,
                'userStatus' => $user->userStatus ? [
                    'id' => $user->userStatus->id,
                    'statusName' => $user->userStatus->status_name,
                ] : null,

                'userProfile' => $user->userProfile ? [
                    'id' => $user->userProfile->id,
                    'titleName' => $user->userProfile->title_name,
                    'fullName' => $user->userProfile->full_name,
                    'nickName' => $user->userProfile->nick_name,
                    'telPhone' => $user->userProfile->tel_phone,
                    'birthDay' => $user->userProfile->birth_day,
                ] : null,

                'userImage' => $user->userImage->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'imageData' => $image->image_data,
                    ];
                }),

                'profileContact' => $user->userProfile?->ProfileContact->map(function ($row) {
                    return $row ? [
                        'id' => $row?->id,
                        'profileID' => $row?->profile_id,
                        'name' => $row?->name,
                        'url' => $row?->url,
                        'icon' => $row?->icon_data
                    ] : null;
                }),

                'userPoint' => $user?->userPoint ? [
                    'id' => $user?->userPoint->id,
                    'user_id' => $user?->userPoint->user_id,
                    'point' => $user?->userPoint->point,
                    'created_at' => $user?->userPoint->created_at,
                    'updated_at' => $user?->userPoint->updated_at,
                ] : null,

                // 'userPointCounter' => $user?->userPoint?->userPointCounter->map(function ($counter) {
                //     return $counter ? [
                //         'id' => $counter?->id,
                //         'user_point_id' => $counter?->user_point_id,
                //         'user_id' => $counter?->user_id,
                //         'reward_id' => $counter?->reward_id,
                //         'point_import' => $counter?->point_import,
                //         'point_export' => $counter?->point_export,
                //         'detail_counter' => $counter?->detail_counter,
                //         'created_at' => $counter?->created_at,
                //         'updated_at' => $counter?->updated_at,
                //     ] : null;
                // }),

            ];

            // dd($userProfile);

            if ($userProfile) {
                return response()->json([
                    'message' => "Laravel get user profile detail success",
                    'userProfile' => $userProfile
                ], 200);
            }

            return response()->json([
                'message' => "laravel get user profile not success.",
                'userProfiles' => $userProfile
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

    public function updateProfile (Request $request) {
        try {
            $request->validate([
                'profileID' => 'required|integer',
                'titleName' => 'nullable|string',
                'fullName' => 'nullable|string',
                'nickName' => 'nullable|string',
                'telPhone' => 'nullable|string',
                'birthDay' => 'nullable|date',
            ]);

            $profile = UserProfile::findOrFail($request->profileID);

            if ($profile) {
                $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
                $profile->update(array_filter([
                    'full_name' => $request->fullName,
                    'nick_name' => $request->nickName,
                    'tel_phone' => $request->telPhone,
                    'birth_day' => $request->birthDay,
                    'updated_at' => $dateTime
                ]));

                return response()->json([
                    'message' => "api update profile successfully",
                    'profileID' => $request->profileID,
                    'profile' => $profile,
                    'status' => 200
                ], 200);

            } else {

                return response()->json([
                    'message' => "api update profile not success",
                    'profileID' => $request->profileID,
                    'status' => 404
                ], 404);

            }

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api user profile controller function update profile error" . $error->getMessage()
            ], 500);
        }
    }

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
