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
                'profileID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',  // 10MB max
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

                $userProfile = UserProfile::findOrFail($request->profileID);

                UserProfileImage::updateOrCreate(
                    ['profile_id' => $userProfile->id],
                    [
                        'image_name' => $imageNameNew,
                        'image_data' => $imageDataBase64,
                    ]
                );

                return response()->json([
                    'message' => "Laravel upload image profile successfully.",
                    'image_data' => $imageDataBase64,
                ], 201);
            }


            return response()->json([
                'message' => "No image file provided.",
            ], 400);
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
                'id' => $userProfiles->id ?? null,
                'email' => $userProfiles->email ?? null,
                'name' => $userProfiles->name ?? null,
                'username' => $userProfiles->username ?? null,
                'statusUser' => $userProfiles->statusUser ? [
                    'id' => $userProfiles->statusUser->id ?? null,
                    'status_name' => $userProfiles->statusUser->status_name ?? null,
                ] : null,
                'userProfile' => $userProfiles->userProfile ? [
                    'id' => $userProfiles->userProfile->id ?? null,
                    'title_name' => $userProfiles->userProfile->title_name ?? null,
                    'full_name' => $userProfiles->userProfile->full_name ?? null,
                    'nick_name' => $userProfiles->userProfile->nick_name ?? null,
                    'tel_phone' => $userProfiles->userProfile->tel_phone ?? null,
                    'birth_day' => $userProfiles->userProfile->birth_day ?? null,
                ] : null,
                'userProfileContact' => $userProfiles->userProfileContact?->map(function ($contact) {
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
                'userProfileImage' => $userProfiles->userProfileImage?->map(function ($profileImage) {
                    return [
                        'id' => $profileImage->id ?? null,
                        'imagePath' => $profileImage->image_path ?? null,
                        'imageName' => $profileImage->image_name ?? null,
                        'imageData' => 'data:image/png;base64,' . base64_encode($profileImage->image_data) ??
                        "https://scontent.fkkc3-1.fna.fbcdn.net/v/t39.30808-6/461897536_3707658799483986_794048670785055411_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=cc71e4&_nc_eui2=AeHVG0UH5FgwbVkdtl70b39it0I862Qbciu3QjzrZBtyK4PmJExwkjQwGNMpc0Sbm9HeXRE2Yi7Fvc_GrvrUrXJN&_nc_ohc=_8IVpzSUJz8Q7kNvgHrEAqC&_nc_oc=AdiyR3fiYNa1nU66Bls4Nb4I6H4rfNRXnPjAHaIZmi5Ok1Ea6cf98AHoc2eD3yBPfT0whk9DJecG4asGH43dv1dt&_nc_zt=23&_nc_ht=scontent.fkkc3-1.fna&_nc_gid=AkN_W4uZTFprgLGSWFrENZx&oh=00_AYCw9q71675W9Lkdf2lM4FSnNCyzhNZ1vmvURll1eRMLQw&oe=67AD49B1",
                    ];
                }) ?? null,
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
