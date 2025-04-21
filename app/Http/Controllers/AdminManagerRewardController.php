<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\RewardImage;

class AdminManagerRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rewards = Reward::with('rewardImage')->get()->map(function ($reward) {
            return $reward ? [
                'id' => $reward->id,
                'name' => $reward->name,
                'point' => $reward->point,
                'amount' => $reward->amount,
                'status' => $reward->status,
                'created_at' => $reward->created_at,
                'updated_at' => $reward->updated_at,
                'rewardImage' => $reward->rewardImage->map(function ($image) {
                    return $image ? [
                        'id' => $image->id,
                        'reward_id' => $image->reward_id,
                        'image_path' => $image->image_path,
                        'image_name' => $image->image_name,
                        'image_data' => $image->image_data ? 'data:image/png;base64,' . $image->image_data : null,
                        'created_at' => $image->created_at,
                        'updated_at' => $image->updated_at,
                    ] : null;
                })
            ] : null;
        });

        return response()->json([
            'message' => 'api get reward success',
            'rewards' => $rewards,
        ], 200);
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
