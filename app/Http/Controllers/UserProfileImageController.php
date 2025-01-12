<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;
use App\Models\ImageUpload;
use Illuminate\Http\Request;

class UserProfileImageController extends Controller
{

    public function uploadImage(Request $request)
    {
        try {
            $user_profile = UserProfile::where('id', $request->profileID)->first();

            if ($user_profile) {
                // Retrieve the uploaded file
                $file = $request->file('file');

                // Generate file name and move the file to the uploads directory
                $fileName = time() . '.' . $file->extension();

                // // เลือก Path ที่จะจัดเก็บไฟล์
                // $path = $request->has('customPath') ? $request->input('customPath') : 'uploads';

                // // ย้ายไฟล์ไปยัง Path ที่เลือก
                // $fullPath = $file->move(public_path($path), $fileName);

                $path = $file->move(public_path('/Vue/src/assets/image-users'), $fileName);

                // อ่านข้อมูล Binary จากไฟล์
                // $fileData = file_get_contents($path);

                // Save image details to the database
                $image_upload = ImageUpload::create([
                    'profile_id' => $request->profileID,
                    // 'image_path' => $path,
                    'image_name' => $fileName,
                    // 'image_data' => $fileData
                ]);

                $user_profile_image = UserProfileImage::create([
                    'profile_id' => $request->profileID,
                    // 'image_path' => $path,
                    'image_name' => $fileName,
                    // 'image_data' => $fileData
                ]);

                return response()->json([
                    'success' => 'File uploaded successfully.',
                    // 'userProfileImage' => [
                    //     'profile_id' => $user_profile_image->profile_id,
                    //     'image_path' => $user_profile_image->image_path,
                    //     'image_name' => $user_profile_image->image_name,
                    // ],
                    // 'image_upload' => [
                    //     'profile_id' => $image_upload->profile_id,
                    //     'image_path' => $image_upload->image_path,
                    //     'image_name' => $image_upload->image_name,
                    // ],
                ], 201);

            } else {
                return response()->json(['error' => 'No file uploaded.'], 422);
            }
        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel api errir",
                'error' => $error->getMessage()()
            ]);
        }
    }

    public function uploadImageNew(Request $req)
    {
        // try {

        //     dd($req->all(), $req->file('file'));

        //     // // ตรวจสอบว่ามีไฟล์หรือไม่
        //     // if (!$request->hasFile('file')) {
        //     //     return response()->json(['error' => 'No file uploaded.'], 422);
        //     // }

        //     // // ดึง User Profile
        //     // $userProfile = UserProfile::where('id', $request->profileID)->first();
        //     // if (!$userProfile) {
        //     //     return response()->json(['error' => 'User profile not found.'], 404);
        //     // }

        //     // // ดึงไฟล์จาก Request
        //     // $file = $request->file('file');

        //     // // สร้างชื่อไฟล์ใหม่
        //     // $fileName = time() . '.' . $file->extension();

        //     // // เลือก Path ที่จะจัดเก็บไฟล์
        //     // $path = $request->has('customPath') ? $request->input('customPath') : 'uploads';

        //     // // ย้ายไฟล์ไปยัง Path ที่เลือก
        //     // $fullPath = $file->move(public_path($path), $fileName);

        //     // // อ่านข้อมูล Binary จากไฟล์
        //     // $fileData = file_get_contents($fullPath);

        //     // // บันทึกข้อมูลรูปภาพลงในฐานข้อมูล
        //     // $imageUpload = ImageUpload::create([
        //     //     'profile_id' => $request->profileID,
        //     //     'image_name' => $fileName,
        //     //     'image_path' => $path . '/' . $fileName,
        //     //     'image_data' => $fileData,
        //     // ]);

        //     // return response()->json([
        //     //     'success' => 'File uploaded successfully.',
        //     //     'image_upload' => $imageUpload,
        //     // ], 201);
        // } catch (\Exception $error) {
        //     return response()->json([
        //         'message' => 'Laravel API error',
        //         'error' => $error->getMessage(),
        //     ], 500);
        // }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            $request->validate([
                'profileID' => 'required|integer',
                'profileImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',
            ]);

            $user_profile = UserProfile::where('id', $request->profileID)->first();

            if ($user_profile) {
                $file = $request->file('profileImage');
                // dd([
                //     '$user_profile' => $user_profile,
                //     'profileID' => $request->profileID,
                //     'file' => $file
                // ]);


                return response()->json([
                    'status' => 200,
                    'message' => "laravel api success",
                    'user_profile' => $user_profile,
                    'profileID' => $request->profileID,
                    'profileImage' => $file
                ]);
            }
        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel api error",
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserProfileImage $profileImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserProfileImage $profileImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserProfileImage $profileImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserProfileImage $profileImage)
    {
        //
    }
}
