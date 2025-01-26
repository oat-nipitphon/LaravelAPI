<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'message' => "get user success.",
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel function index user controller error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function popProfile (Request $req, user $user, string $userID) {
        try {

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel pop profile function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function followersAccount (Request $req, user $user, string $userID) {
        try {

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel pop profile function error",
                'error' => $error->getMessage()
            ], 500);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
