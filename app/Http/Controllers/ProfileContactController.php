<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ProfileContact;
use Illuminate\Http\Request;

class ProfileContactController extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::with([
            'userProfile',
            'userProfile.profileContact' // ใช้ชื่อให้ตรงกับที่กำหนดใน Model
        ])->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'userProfile' => $row->userProfile ? [
                    'fullName' => $row->userProfile->full_name
                ] : null,
                'profileContacts' => $row->userProfile ?
                    $row->userProfile->profileContact->map(function ($contact) {
                        return [
                            'name' => $contact->name,
                            'url' => $contact->url,
                            'icon' => $contact->icon_data
                        ];
                    }) : [],
            ];
        });

        dd($user);

        return response()->json([
            'message' => "api profile contact success",
            'user' => $user
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    // FUNCTION STORE
         /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            // dd('function store', $request);

            $request->validate([
                'profileID' => 'required|integer',
                'contacts' => 'required|array|min:1',
                'contacts.*.name' => 'required|string',
                'contacts.*.url' => 'required|string',
                'contacts.*.iconFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $contacts = $request->input('contacts', []);

            // dd($contacts);

            foreach ($contacts as $index => $contact) {
                // ***** insert data > 1
                // ***** creact model inside loop
                $profileContact = new ProfileContact();

                if ($request->hasFile("contacts.{$index}.iconFile")) {
                    $fileIcon = $request->file("contacts.{$index}.iconFile");
                    $iconData = file_get_contents($fileIcon->getRealPath());
                    $iconDatabase64 = base64_encode($iconData);
                } else {
                    $iconDatabase64 = null;
                }

                $profileContact->profile_id = $request->profileID;
                $profileContact->name = $contact['name'];
                $profileContact->url = $contact['url'];
                $profileContact->icon_data = $iconDatabase64;
                $profileContact->save();
            }

            if (!$profileContact->id) {
                return response()->json([
                    'message' => "Failed to save profile contact",
                ], 500);
            }

            return response()->json([
                'message' => "api save profile contact success",
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api save contact profile function store error" . $error->getMessage()
            ]);
        }

    }

    public function newContact(Request $request)
    {

        try {

            // dd('function store', $request);

            $request->validate([
                'profileID' => 'required|integer',
                'contacts' => 'required|array|min:1',
                'contacts.*.name' => 'required|string',
                'contacts.*.url' => 'required|string',
                'contacts.*.iconFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $contacts = $request->input('contacts', []);

            // dd($contacts);

            foreach ($contacts as $index => $contact) {
                // ***** insert data > 1
                // ***** creact model inside loop
                $profileContact = new ProfileContact();

                if ($request->hasFile("contacts.{$index}.iconFile")) {
                    $fileIcon = $request->file("contacts.{$index}.iconFile");
                    $iconData = file_get_contents($fileIcon->getRealPath());
                    $iconDatabase64 = base64_encode($iconData);
                } else {
                    $iconDatabase64 = null;
                }

                $profileContact->profile_id = $request->profileID;
                $profileContact->name = $contact['name'];
                $profileContact->url = $contact['url'];
                $profileContact->icon_data = $iconDatabase64;
                $profileContact->save();
            }

            if (!$profileContact->id) {
                return response()->json([
                    'message' => "Failed to save profile contact",
                ], 500);
            }

            return response()->json([
                'message' => "api save profile contact success",
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api save contact profile function store error" . $error->getMessage()
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(ProfileContact $profileContact, int $profileID)
    {
        try {

            $profile = UserProfile::findOrFail($profileID);
            $contactProfiles = $profile->profileContact;
            dd($contactProfiles);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "api get contact profile function error" . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfileContact $profileContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProfileContact $profileContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfileContact $profileContact)
    {
        //
    }
}
