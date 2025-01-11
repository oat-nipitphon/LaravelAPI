<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use App\Models\UserProfileImage;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{

    public function uploadImage(Request $request)
    {
        $user_profile = UserProfile::where('id', $request->profileID)->first();

        if ($user_profile) {
            // Retrieve the uploaded file
            $file = $request->file('file');

            // Generate file name and move the file to the uploads directory
            $fileName = time() . '.' . $file->extension();

            $path = $file->move(public_path('uploads'), $fileName);

            // Read file contents
            $fileData = file_get_contents($path);

            // Save image details to the database
            $image_upload = ImageUpload::create([
                'profile_id' => $request->profileID,
                'image_name' => $fileName,
                // 'image_path' => $path,
                // 'image_data' => $fileData
            ]);

            $user_profile_image = UserProfileImage::create([
                'profile_id' => $request->profileID,
                'image_name' => $fileName,
                // 'image_path' => $path,
                // 'image_data' => $fileData
            ]);

            return response()->json([
                'success' => 'File uploaded successfully.',
                'userProfileImage' => $user_profile_image,
                'image_upload' => $image_upload,
                // 'file' => $file,
                // 'file_name' => $fileName,
                // 'file_data' => $fileData,
                // 'path' => $path
            ], 201);

        } else {
            return response()->json(['error' => 'No file uploaded.'], 422);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageUpload $imageUpload)
    {
        //
    }
}
