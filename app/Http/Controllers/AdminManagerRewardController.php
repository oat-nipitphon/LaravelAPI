<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
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
        try {


            return response()->json([
                'message' => ''
            ], 200);
        } catch (\Exception $error) {

            Log::error("api function store error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "api function store error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {


            return response()->json([
                'message' => ''
            ], 200);
        } catch (\Exception $error) {

            Log::error("api function show error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "api function show error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {


            return response()->json([
                'message' => ''
            ], 200);
        } catch (\Exception $error) {

            Log::error("api function update", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "api function update",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $rewardID)
    {
        try {

            $reward = Reward::findOrFail($rewardID);
            $reward->delete();

            return response()->json([
                'message' => 'api destroy success'
            ], 200);

        } catch (\Exception $error) {

            Log::error("api function destroy error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "api function destroy error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function updateStatusReward(Request $request, string $rewardID)
    {

        try {



            $reward = Reward::findOrFail($rewardID);


            if ($request->status === 'true') {
                $reward->update([
                    'status' => 'false',
                    'updated_at' => now()
                ]);
            } else if ($request->status === 'false') {
                $reward->update([
                    'status' => 'true',
                    'updated_at' => now()
                ]);
            } else {
                $reward->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);
            }

            if (!$reward) {
                return response()->json([
                    'message' => 'api update reward status false',
                    'reward' => $rewardID,
                    'status' => $request->status
                ], 404);
            }

            return response()->json([
                'message' => 'api update reward status success',
                'reward' => $reward,
            ], 200);


        } catch (\Exception $error) {

            Log::error("api function update status reward error ", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "api function update status reward error ",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
