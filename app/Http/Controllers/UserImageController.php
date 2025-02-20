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

    private function dateTimeFormatTimeZone () {
        return Carbon::now('Asia/bangkok')->format('Y-m-d H:i:s');
    }

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

                $userImage = UserImage::where('user_id', $request->userID)->first();

                if ($userImage) {
                    $checkStatus = $userImage->update([
                        'image_data' => $imageDataBase64,
                        'updated_at' => $this->dateTimeFormatTimeZone()
                    ]);

                } else {
                    $checkStatus = UserImage::create([
                        'user_id' => $validated['userID'],
                        'image_data' => $imageDataBase64,
                        'created_at' => $this->dateTimeFormatTimeZone(),
                    ]);
                }

                return response()->json([
                    'message' => "api user upload image success",
                    'userImage' => $userImage,
                    'checkStatus' => $checkStatus,
                ], 200);

            }

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api upload user image function error" . $error->getMessage(),
            ]);
        }
    }
}
