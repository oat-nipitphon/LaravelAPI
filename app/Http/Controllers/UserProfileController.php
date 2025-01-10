<?php

namespace App\Http\Controllers;

use App\Models\User;
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

            $fields = $request->validate([
                'userID' => 'request|integer',
                'name' => 'request|string',
                'email' => 'request|string',
                'username' => 'request|string',
                'status' => 'request|string',
                'profileID' => 'request|integer',
                'titleName' => 'request|string',
                'fullName' => 'request|string',
                'nickName' => 'request|string'
            ]);

            if (!$fields) {
                dd([
                    'fields' => $fields,
                    'request' => $request->all()
                ]);
            }

            $user_profiles = User::all();

            $user_profiles->with('user_profile')->where(function ($query) use ($fields) {
                $query->where('id', $fields['userID'])
                ->orWhere('profile_id', $fields['profileID']);
            });

            dd($user_profiles);


        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel api function error",
                'error' => $error->getMessage()
            ]);
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
                // dd($userProfile, $id);
            }

            return response()->json([
                'message' => "laravel get user profile function show success.",
                // 'userProfile' => $userProfile,
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
