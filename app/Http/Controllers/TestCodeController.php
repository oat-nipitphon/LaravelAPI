<?php

namespace App\Http\Controllers;

use App\Models\TestCode;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Post;

use Illuminate\Http\Request;


class TestCodeController extends Controller
{

    public function uploadImage(Request $request)
    {
        try {

            dd($request->all());

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->getMessage(),
            ], 422);
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
    public function show(TestCode $testCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TestCode $testCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestCode $testCode)
    {
        //
    }
}
