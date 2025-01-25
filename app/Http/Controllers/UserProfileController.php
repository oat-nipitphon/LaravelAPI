<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user_profile = User::with('userProfile')->get();

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

            $userProfile = UserProfile::findOrFail($validated['profileID']);

            if ($userProfile) {

                $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');
                $dateTimeUpdate = Carbon::now('Asia/Bangkok')->setTimezone('UTC');
                $userProfile->user->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTimeUpdate,
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
                ], 400);
            }

        } catch (\Exception $error) {
            // การจัดการข้อผิดพลาด
            return response()->json([
                'message' => 'Laravel api function error :: ',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function uploadImageProfile(Request $req)
    {
        try {
            // Validate the request
            $req->validate([
                'profileID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',
            ]);

            // Find the user profile
            $userProfile = UserProfile::findOrFail($req->profileID);

            if (!$req->hasFile('imageFile')) {
                return response()->json([
                    'message' => "No image file provided.",
                ], 400);
            }

            // Handle the image file
            $imageFile = $req->file('imageFile');
            $imagePath = $imageFile->store('profile_images', 'public');
            $imageName = $imageFile->getClientOriginalName();
            $imageNameNew = time() . " - " . $imageName;
            $imageData = file_get_contents($imageFile->getRealPath());

            // Save the image data in the database
            UserProfileImage::create([
                'profile_id' => $req->profileID,
                'image_path' => $imagePath,
                'image_name' => $imageNameNew,
                'image_data' => $imageData,
            ]);

            return response()->json([
                'message' => "Image uploaded successfully.",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "An error occurred.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id, userProfile $userProfile)
    {
        try {

            $userProfile = UserProfile::findOrFail($userProfile->id);

            $userProfile->with(
                'userProfileContact',
                'userProfileImage',
                'user',
                'user.userLogin',
                'user.statusUser',
                'user.posts',
                'user.userFollowersProfile',
                'user.userFollowersAccount'
            )->first();

            if ($userProfile) {
                return response()->json([
                    'message' => "laravel get user profile successfully",
                    'status' => true,
                    'userProfile' => $userProfile
                ], 200);
            } else {
                return response()->json([
                    'message' => "laravel get user profile not success.",
                    'status' => false,
                    'userProfile' => $userProfile
                ], 400);
            }


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
            $dateTimeUpdate = Carbon::now('Asia/Bangkok')->setTimezone('UTC');
            if ($userProfile) {

                // ใช้ข้อมูลที่ผ่านการ validate มาอัพเดท
                $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');
                $userProfile->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTimeUpdate
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
