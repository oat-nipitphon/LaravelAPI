<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facedes\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfileImage;

class UserProfileImageController extends Controller
{




    /**
     * Resize image before saving to database
     * @param UploadedFile $image
     * @param int $width
     * @param int $height
     * @return string (base64)
     */
    private function resizeImage($image, $width, $height)
    {

        $resizeImage = Image::make($image->getRealPath())
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->encode('jpg', 'png', 75);

        return base64_encode($resizeImage);
    }

    public function uploadImageProfile(Request $request)
    {
        try {

            $request->validate([
                'userID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            if ($request->file('imageFile')) {

                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);
                // $imageDataBase64 = $this->resizeImage($imageFile, 300, 300);

                $userProfileImage = UserProfileImage::where('user_id', $request->userID)
                    ->updateOrCreate(
                        ['user_id' => $request->userID],
                        [
                            'image_data' => $imageDataBase64,
                            'updated_at' => $dateTime
                        ]
                    );

                // UserProfileImage::updateOrCreate(
                //     ['user_id' => $request->userID],
                //     [
                //         'image_data' => $imageDataBase64,
                //         'updated_at' => $dateTime,
                //         'created_at' => $dateTime,
                //     ]
                // );

                if (!$userProfileImage) {
                    return response()->json([
                        'message' => "Laravel upload image user profile failed.",
                    ], 400);
                }

                return response()->json([
                    'message' => "Laravel upload image user profile successfully."
                ], 201);
            }
        } catch (\Exception $error) {
            Log::error('Image upload error: ' . $error->getMessage());

            return response()->json([
                'message' => "An error occurred during image upload." . $error->getMessage(),
            ], 500);
        }
    }

    private function dateTimeNowFormatTimeZone()
    {
        $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
        return $dateTime;
    }

    public function uploadImageUserProfile(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2040'
            ]);

            $user = User::findOrFail($request->userID);

            if ($user) {

                if (!$request->hasFile('imageFile')) {
                    return response()->json([
                        'hasFile' => $request->file('imageFile'),
                    ]);
                }

                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                if ($imageDataBase64) {

                    $userProfileImage = UserProfileImage::where('user_id', $user->id)->first();

                    if ($userProfileImage) {

                        $checkUpdate = [
                            'status' => 200,
                            'message' => "update user profile image",
                            'userProfileImage' =>  $userProfileImage->update([
                                'image_data' => $imageDataBase64,
                                'updated_at' => $this->dateTimeNowFormatTimeZone(),
                            ]),
                        ];
                    } else {

                        $checkUpdate = [
                            'status' => 200,
                            'message' => "update user profile image",
                            'userProfileImage' =>  UserProfileImage::create([
                                'user_id' => $user->id,
                                'image_data' => $imageDataBase64,
                                'created_at' => $this->dateTimeNowFormatTimeZone(),
                            ]),
                        ];
                    }
                }

                if ($checkUpdate['status'] === 200) {
                    return response()->json([
                        'message' => "upload user profile image success",
                        'imageData' => $imageDataBase64,
                    ]);
                }

                return response()->json([
                    'message' => "upload user profile image false",
                    'imageData' => $imageDataBase64
                ]);
            }
        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel api upload image error",
                'error' => $error->getMessage()
            ]);
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
