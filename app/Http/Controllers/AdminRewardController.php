<?php

namespace App\Http\Controllers;

use App\Models\AdminReward;
use App\Models\Reward;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminRewardController extends Controller
{
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminReward $adminReward)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminReward $adminReward)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminReward $adminReward, string $rewardID)
    {
        $reward = Reward::findOrFail($rewardID);

        return response()->json([
            'message' => 'laravel api update reward',
            'reward' => $reward
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminReward $adminReward)
    {
        //
    }
}
