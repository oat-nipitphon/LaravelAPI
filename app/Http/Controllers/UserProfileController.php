<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
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
            $user = User::findOrFail($validated['userID']);
            $userProfile = $user->user_profile;
            if ($userProfile) {

                // ใช้ข้อมูลที่ผ่านการ validate มาอัพเดท

                $userProfile->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'username' => $validated['userName'],
                    'status_id' => $validated['statusID'],
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => Carbon::parse($validated['birthDay'])->format('Y-m-d'),
                ]);

                return response()->json([
                    'message' => 'Profile updated successfully.',
                    'user' => $user
                ], 200);
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

    public function uploadImageUserProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'userID' => 'required|integer',
                'fileImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',
            ]);

            $userProfile = UserProfile::where('user_id', $validated['userID'])->first();

            if (!$userProfile) {
                return response()->json(['message' => 'User profile not found'], 404);
            }

            if ($request->hasFile('fileImage')) {
                $file = $request->file('fileImage');
                $imagePath = $file->store('image-user-profile', 'public');
                $imageName = $file->getClientOriginalName();

                $userProfileImage = UserProfile::updateOrCreate(
                    ['profile_id' => $userProfile->id],
                    ['image_path' => $imagePath, 'image_name' => $imageName]
                );

                return response()->json([
                    'message' => 'Image uploaded successfully.',
                    'user_profile_image' => $userProfileImage,
                ], 201);
            }

            return response()->json(['message' => 'No file uploaded'], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error uploading image.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            }

            return response()->json([
                'message' => "laravel get user profile function show success.",
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
