<?php

namespace App\Http\Controllers;

use App\Models\AdminManagerUserProfile;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class AdminManagerUserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userProfiles = UserProfile::all();

            if (!$userProfiles) {
                return response()->json([
                    'message' => "Laravel admin manager user profile false.",
                    'userProfiles' => $userProfiles
                ], 400);
            }

            return response()->json([
                'message' => "Laravel admin manager user profile success.",
                'userProfiles' => $userProfiles
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Laravel admin manager user profile error.",
                'error' => $e->getMessage()
            ]);
        }
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
    public function show(AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminManagerUserProfile $adminManagerUserProfile)
    {
        //
    }
}
