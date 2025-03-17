<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Reward;
use App\Models\RewardImage;

class RewardController extends Controller
{

    // Function format date time
    private function dateTimeFormatTimeZone()
    {
        return Carbon::now('Asia/bangkok')->format('Y-m-d H:i:s');
    }

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
                'quantity' => $reward->quantity,
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

            $validatedData = $request->validate([
                'name' => 'required|string',
                'point' => 'required|numeric',
                'quantity' => 'required|integer',
                'status' => 'required|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $modelReward = Reward::create([
                'name' => $validatedData['name'],
                'point' => $validatedData['point'],
                'quantity' => $validatedData['quantity'],
                'status' => $validatedData['status']
            ]);

            if ($modelReward) {

                if ($request->hasFile('imageFile')) {

                    $imageFile = $request->file('imageFile');
                    $imageData = file_get_contents($imageFile->getRealPath());
                    $imageDataBase64 = base64_encode($imageData);

                    $modelRewardImage = RewardImage::create([
                        'reward_id' => $modelReward->id,
                        'image_data' => $imageDataBase64,
                        'created_at' => $this->dateTimeFormatTimeZone(),
                    ]);

                    if ($modelRewardImage) {
                        return response()->json([
                            'message' => 'Reward created successfully',
                            'reward' => $modelReward,
                            'image_path' => $imageDataBase64
                        ], 201);
                    }
                }
            }

            return response()->json([
                'message' => 'reward created false',
                'reward' => $modelReward,
                'imageDataBase64' => $imageDataBase64
            ], 404);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'api function store create reward error',
                'error' => $error->getMessage()
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {

            $rewards = Reward::with('rewardImage')->findOrFail($id);

            $rewards = [
                'id' => $rewards->id,
                'name' => $rewards->name,
                'point' => $rewards->point,
                'quantity' => $rewards->quantity,
                'status' => $rewards->status,
                'created_at' => $rewards->created_at,
                'updated_at' => $rewards->updated_at,
                'rewardImage' => $rewards->rewardImage->map(function ($image) {
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
            ];

            if (!$rewards) {
                return response()->json([
                    'message' => 'api function show response false',
                    'id' => $id
                ], 204);
            }

            return response()->json([
                'message' => 'api function show response success',
                'rewards' => $rewards
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'api function show error',
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {

            $checkUpdate = false;

            $validatedData = $request->validate([
                'rewardID' => 'required|integer',
                'name' => 'required|string',
                'point' => 'required|numeric',
                'quantity' => 'required|integer',
                'status' => 'required|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $modelReward = Reward::findOrFail($request->rewardID);

            if (!$modelReward) {
                return response()->json([
                    'message' => 'api update reward response false',
                    'rewardID' => $request->all()
                ]);
            }

            $modelReward->update([
                'name' => $request->name,
                'point' => $request->point,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'updated_at' => $this->dateTimeFormatTimeZone()
            ]);

            $modelRewardImage = RewardImage::where('reward_id', $modelReward->id)->first();

            if ($request->hasFile('imageFile') && $modelRewardImage) {

                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                $modelRewardImage->update([
                    'image_data' => $imageDataBase64,
                    'updated_at' => $this->dateTimeFormatTimeZone()
                ]);

                $checkUpdate = true;
            }


            if ($checkUpdate === true) {

                return response()->json([
                    'message' => 'api update reward success',
                    'status' => 200
                ], 200);

            } else {

                return response()->json([
                    'message' => 'api update reward success',
                    'status' => 404
                ], 404);

            }

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'api function update error',
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {

            $modelReward = Reward::findOrFail($id);
            $modelRewardImage = RewardImage::where('reward_id', $modelReward->id)->first();

            if (!$modelReward && $modelRewardImage) {
                return response()->json([
                    'message' => 'api delete reward false'
                ], 404);
            }

            $modelReward->delete();
            $modelRewardImage->delete();

            return response()->json([
                'message' => 'api delete reward success',
                'modelReward' => $modelReward
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'api function destroy error',
                'error' => $error->getMessage()
            ]);
        }
    }
}
