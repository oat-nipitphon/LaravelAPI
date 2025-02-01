<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            // $user_profile = User::with('userProfile')->get();
            // $user_profile = new User();

            $user_profile = User::with(
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
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',  // 10MB max
            ]);

            if (!$req->hasFile('imageFile')) {
                return response()->json([
                    'message' => "No image file provided.",
                ], 400);
            }

            // Handle the image file
            $imageFile = $req->file('imageFile');
            $imagePath = $imageFile->store('profile_images', 'public');  // Store image on 'public' disk
            $imageName = $imageFile->getClientOriginalName();
            $imageNameNew = time() . " - " . $imageName;  // Unique name with timestamp

            // Read image file content for blob storage
            $imageData = file_get_contents($imageFile->getRealPath());
            $imageDataBase64 = base64_encode($imageData);  // Base64 encode the image data

            // Find the user profile
            // $userProfile = UserProfile::findOrFail($req->profileID);
            // if ($userProfile) {
            //     $userProfile->update([
            //         'profile_id' => $req->profileID,
            //         'image_path' => $imagePath,
            //         'image_name' => $imageNameNew,
            //         'image_data' => $imageDataBase64,  // Save base64 encoded da
            //     ]);
            // }

            // Now store or return the base64 encoded image
            UserProfileImage::create([
                'profile_id' => $req->profileID,
                'image_path' => $imagePath,
                'image_name' => $imageNameNew,
                'image_data' => $imageDataBase64,  // Save base64 encoded data
            ]);

            return response()->json([
                'message' => "Image uploaded successfully.",
                'image_url' => asset('storage/' . $imagePath),  // Return URL of the image
                'image_data' => $imageDataBase64,  // Return base64 encoded image data (if needed)
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging purposes (optional)
            Log::error('Image upload error: ' . $e->getMessage());

            return response()->json([
                'message' => "An error occurred during image upload.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // public function uploadImageProfile(Request $req)
    // {
    //     try {

    //         // Validate the request
    //         $req->validate([
    //             'profileID' => 'required|integer',
    //             'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',
    //         ]);

    //         // Find the user profile
    //         $userProfile = UserProfile::findOrFail($req->profileID);

    //         if (!$req->hasFile('imageFile')) {
    //             return response()->json([
    //                 'message' => "No image file provided.",
    //             ], 400);
    //         }

    //         // Handle the image file
    //         $imageFile = $req->file('imageFile');
    //         $imagePath = $imageFile->store('profile_images', 'public');
    //         $imageName = $imageFile->getClientOriginalName();
    //         $imageNameNew = time() . " - " . $imageName;

    //         $imageData = file_get_contents($imageFile->getRealPath());

    //         // Save the image data in the database
    //         UserProfileImage::create([
    //             'profile_id' => $req->profileID,
    //             'image_path' => $imagePath,
    //             'image_name' => $imageNameNew,
    //             'image_data' => $imageData,
    //         ]);

    //         return response()->json([
    //             'message' => "Image uploaded successfully.",
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => "An error occurred.",
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {

            $user = User::with(
                'userProfileContact',
                'userProfile.userProfileImage',
                'userLogin',
                'statusUser',
                'posts',
                'userFollowersProfile',
                'userFollowersAccount'
            )->findOrFail($id);
            // Transform the user profile data
            $userProfiles = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'username' => $user->username,
                'userProfileContact' => $user->userProfileContact->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'contact_name' => $contact->contact_name,
                        'contact_link_path' => $contact->contact_link_path,
                        'contact_icon_name' => $contact->contact_icon_name,
                        'contact_icon_url' => $contact->contact_icon_url,
                        'contact_icon_data' => $contact->contact_icon_data ? 'data:image/png;base64,' . base64_encode($contact->contact_icon_data) : null, // ✅ Fixed here
                    ];
                }),
                'userProfile' => [
                    'id' => $user->userProfile->id,
                    'title_name' => $user->userProfile->title_name,
                    'full_name' => $user->userProfile->full_name,
                    'nick_name' => $user->userProfile->nick_name,
                    'tel_phone' => $user->userProfile->tel_phone,
                    'birth_day' => $user->userProfile->birth_day,
                ],
                'statusUser' => [
                    'id' => $user->statusUser->id,
                    'status_name' => $user->statusUser->status_name,
                ],
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
            ], 400);
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
