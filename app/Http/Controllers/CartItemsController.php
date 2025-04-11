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
            $user = User::with('userPoint', 'userPointCounter')->findOrFail($userID);

            return response()->json([
                'message' => 'Laravel API get report reward success.',
                'userPointCounter' => $user
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Laravel API function get report reward error',
                'error' => $error->getMessage()
            ], 500);
        }
    }


}
