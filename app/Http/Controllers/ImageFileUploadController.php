<?php

namespace App\Http\Controllers;

use App\Models\ImageFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageFileUploadController extends Controller
{

    public function uploadFile(Request $request) {
        // $file = Input::file('file');
        // $filename = $file->getClientOriginalName();

        // $path = hash( 'sha256', time());

        // if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($file))) {
        //     $input['filename'] = $filename;
        //     $input['mime'] = $file->getClientMimeType();
        //     $input['path'] = $path;
        //     $input['size'] = $file->getClientSize();
        //     $file = FileEntry::create($input);

        //     return response()->json([
        //         'success' => true,
        //         'id' => $file->id
        //     ], 200);
        // }
        // return response()->json([
        //     'success' => false
        // ], 500);
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
        try {

            $request->validate([
                'imageFile' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'profileID' => 'required|integer'
            ]);


            return response()->json([
                'message' => 'File uploaded successfully'
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel function error",
                'error' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ImageFileUpload $imageFileUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImageFileUpload $imageFileUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageFileUpload $imageFileUpload)
    {
        //
    }
}
