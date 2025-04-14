<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\UserPointCounter;
use App\Models\Reward;
use Carbon\Carbon;

class CartItemsController extends Controller
{

    private function dateTimeFormatTimeZone()
    {
        return Carbon::now('Asia/bangkok')->format('Y-m-d H:i:s');
    }

    public function userConfirmSelectReward(Request $request)
    {
        try {

            $userPoint = new UserPoint();
            $userPointCounter = new UserPointCounter();
            $reward = new Reward();

            $request->validate([
                'userID' => 'required|integer',
                'userAmount' => 'required|integer',
                'counterItems' => 'required|json',
            ]);

            $counterItems = $request->input('counterItems');

            $userPoint = UserPoint::where('user_id', $request->userID)->first();

            if (!$userPoint) {

                return response()->json([
                    'message' => 'laravelapi user point request false',
                    'userID' => $request->userID,
                ], 404);
            }

            $userPoint->update([
                'point' => $request->userAmount,
                'updated_at' => $this->dateTimeFormatTimeZone()
            ]);

            if (!$counterItems) {
                return response()->json([
                    'message' => 'laravelapi counter item request false',
                    'counterItems' => $counterItems = $request->input('counterItems'),
                ], 404);
            }

            $checkStatusCounter = false;
            foreach(json_decode($counterItems) as $item) {
                $data = [
                    'rewardID' => $item->rewardID,
                    'rewardName' => $item->rewardName,
                    'rewardPoint' => $item->rewardPoint,
                    'rewardAmount' => $item->rewardAmount,
                ];

                UserPointCounter::create([
                    'user_id' => $request->userID,
                    'reward_id' => $data['rewardID'],
                    'point_status' => $data['rewardPoint'],
                    'created_at' => $this->dateTimeFormatTimeZone()
                ]);

                // จำนวนคงเหลือ ของรางวัล
                // $reward = Reward::findOrFail($data['rewardID']);
                // $

                $checkStatusCounter = true;
            }


            if ($checkStatusCounter !== true) {

                return response()->json([
                    'userPoint' => $userPoint,
                    'counterItems' => $counterItems,
                    'message' => 'laravelapi create reward user point counter false',
                ], 404);

            }

            return response()->json([
                'userPoint' => $userPoint,
                'counterItems' => $counterItems,
                'message' => 'laravelapi create reward user point counter success.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'api function userConfirmSelectReward error' . $e->getMessage()
            ]);
        }
    }

    public function getReportReward(Request $request, string $userID)
    {
        try {
            // ดึงข้อมูลเฉพาะผู้ใช้ พร้อม relation
            $user = User::with('userPoint', 'userPointCounter', 'userPointCounter.reward')->findOrFail($userID);

            $userPointCounters = [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_username' => $user->username,
                'user_point' => $user->userPoint->point,
                'counters' => $user->userPointCounter?->map(function ($counter) {
                    return $counter ? [
                        'id' => $counter->id,
                        'user_id' => $counter->user_id,
                        'detail' => $counter->detail_counter,
                        'created_at' => $counter->created_at,
                        'updated_at' => $counter->updated_at,
                        'rewards' => $counter->reward->map(function ($reward) {
                            return $reward ? [
                                'id' => $reward->id,
                                'point' => $reward->point,
                                'images' => $reward->rewardImage->map(function ($image) {
                                    return $image ? [
                                        'id' => $image->id,
                                        'image_data' => $image->image_data,
                                    ] : null;
                                }),
                            ] : null;
                        }),
                    ] : null;
                }),
            ];

            return response()->json([
                'message' => 'Laravel API get report reward success.',
                // 'userPointCounter' => $user,
                'userPointCounter' => $userPointCounters,
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Laravel API function get report reward error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function cancelReward (string $rewardID, string $userID) {
        try {
            $userPointCounter = UserPointCounter::where('reward_id', $rewardID, 'user_id', $userID)->first();
            // dd($userPointCounter);

            return response()->json([
                'message' => 'laravelapi function cancel reward success',
                'cancel' => $userPointCounter,
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'function cancel reward error',
                'error' => $$error->getMessage(),
            ], 404);
        }
    }

}
