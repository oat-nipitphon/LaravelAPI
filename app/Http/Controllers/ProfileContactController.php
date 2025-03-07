<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ProfileContact;
use Illuminate\Http\Request;

class ProfileContactController extends Controller
{

        /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            $request->validate([
                'profileID' => 'required|integer',
                'contacts' => 'required|array|min:1',
                'contacts.*.name' => 'required|string',
                'contacts.*.url' => 'required|string',
                'contacts.*.iconFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $contacts = $request->input('contacts', []);

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
        // รับข้อมูลจาก request
        $validate = $request->validate([
            'userID' => 'required|integer',
            'profileID' => 'required|integer',
        ]);

        $contacts = $request->input('contacts', []);

        $checkNewContact = false;

        foreach ($contacts as $contact) {


            // ตรวจสอบว่า contact นี้มีไฟล์ภาพหรือไม่
            if (isset($contact['imageIcon'])) {
                // ถ้ามีไฟล์ภาพให้ทำการอัปโหลดและแปลงเป็น base64
                $image = $contact['imageIcon'];
                $iconData = file_get_contents($image->getRealPath());
                $iconDatabase64 = base64_encode($iconData);

                // สร้าง contact ใหม่
                $profileContact = ProfileContact::create([
                    'profile_id' => $validate['profileID'],
                    'name' => $contact['name'],
                    'url' => $contact['url'],
                    'icon_data' => $iconDatabase64
                ]);

                // อัปเดต icon data ใน contact
                // $profileContact->update([

                // ]);
            }

            // อัปเดตสถานะว่าได้บันทึก contact แล้ว
            $checkNewContact = true;
        }

        // ตรวจสอบว่าได้บันทึกข้อมูลหรือไม่
        if ($checkNewContact) {
            return response()->json([
                'message' => "New contact successfully added"
            ], 201);
        } else {
            return response()->json([
                'message' => "Failed to add new contact"
            ], 204);
        }
    }

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
