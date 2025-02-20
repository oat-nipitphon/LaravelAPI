<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserImage;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserImageController extends Controller
{
    public function uploadUserImage (Request $request) {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'imageFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                $userImage = UserImage::create([
                    'user_id' => $validated['userID'],
                    'image_data' => $imageDataBase64,
                ]);

            }

            if (!$userImage) {
                return response()->json([
                    'message' => "upload user image false",
                    'image_data' => $imageDataBase64
                ], 204);
            }

            return response()->json([
                'message' => "upload user image true",
                'image_data' => $imageDataBase64
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api upload user image function error" . $error->getMessage(),
            ]);
        }
    }
}
