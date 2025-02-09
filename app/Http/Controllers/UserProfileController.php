<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;


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

    public function uploadImageProfile(Request $request)
    {
        try {

            $request->validate([
                'userID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  // 10MB max
            ]);

            if ($request->hasFile('imageFile')) {

                $imageFile = $request->file('imageFile');
                // $imagePath = $imageFile->store('profile_images', 'public');
                $imageName = $imageFile->getClientOriginalName();
                $imageNameNew = time() . " - " . $imageName;

                // File image blob binary
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                // File image resize
                // $imageResize = Image::read($imageFile->path());
                // $imageResize->resize(100, 100, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->save($imagePath. '/' .$imageNameNew);
            }

            // Optionally, if you need to filter or retrieve a specific result, you can do that separately
            // $profileImage = $user->userProfileImage()->where('user_id', $request->userID)->first();

            $user = User::findOrFail($request->userID);
            if ($user) {
                $userProfileImage = UserProfileImage::where('user_id', $user->id)->first();
                $userProfileImage->update([
                    'user_id' => $user->id,
                    'image_name' => $imageNameNew,
                    'image_data' => $imageDataBase64,
                ]);

                if ($userProfileImage) {
                    return response()->json([
                        'message' => "Laravel upload image successfully.",
                        'userProfileImage' => $userProfileImage,
                    ], 201);
                }
            }


            return response()->json([
                'message' => "upload image false",
            ], 204);
        } catch (\Exception $e) {

            Log::error('Image upload error: ' . $e->getMessage());

            return response()->json([
                'message' => "An error occurred during image upload.",
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

            $userProfiles = User::with(
                'userProfileContact',
                'userProfileImage',
                'userLogin',
                'statusUser',
                'posts',
                'userFollowersProfile',
                'userFollowersAccount'
            )->findOrFail($id);

            $userProfiles = [
                'id' => $userProfiles->id,
                'email' => $userProfiles->email,
                'name' => $userProfiles->name,
                'username' => $userProfiles->username,

                'statusUser' => $userProfiles->statusUser ? [
                    'id' => $userProfiles->statusUser->id,
                    'status_name' => $userProfiles->statusUser->status_name,
                ] : null,

                'userProfile' => $userProfiles->userProfile ? [
                    'id' => $userProfiles->userProfile->id,
                    'title_name' => $userProfiles->userProfile->title_name,
                    'full_name' => $userProfiles->userProfile->full_name,
                    'nick_name' => $userProfiles->userProfile->nick_name,
                    'tel_phone' => $userProfiles->userProfile->tel_phone,
                    'birth_day' => $userProfiles->userProfile->birth_day,
                ] : null,

                'userProfileContact' => $userProfiles->userProfileContact->map(function ($contact) {
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
                'userProfileImage' => $userProfiles->userProfileImage->map(function ($profileImage) {
                    return [
                        'id' => $profileImage->id,
                        'imagePath' => $profileImage->image_path,
                        'imageName' => $profileImage->image_name,
                        'imageData' => 'data:image/png;base64,' . base64_encode($profileImage->image_data),
                    ];
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
