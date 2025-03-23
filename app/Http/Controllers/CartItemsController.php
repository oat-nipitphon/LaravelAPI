<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                'totalPoint' => 'required|integer',
                // 'counterItems' => 'required|json',
            ]);

            $counterItems = $request->input('counterItems');

            $userPoint = UserPoint::where('user_id', $request->userID)->first();

            if ($userPoint) {

                $userPoint->update([
                    'point' => $request->totalPoint,
                    'updated_at' => $this->dateTimeFormatTimeZone()
                ]);


                foreach (json_decode($counterItems) as $item) {

                    $data = [
                        'rewardID' => $item->rewardID,
                        'rewardName' => $item->rewardName,
                        'rewardPoint' => $item->rewardPoint,
                        'rewardAmount' => $item->rewardAmount,
                    ];

                    $reward->findOrFail($data['rewardID']);

                    if ($reward->amount < $data['rewardAmount']) {

                        $reward->update([
                            'amount_status' => 'false',
                            'updated_at' => $this->dateTimeFormatTimeZone()
                        ]);
                    }

                    $reward->update([
                        'amount' => $reward->amount - $data['rewardAmount'],
                        'updated_at' => $this->dateTimeFormatTimeZone()
                    ]);


                    $userPointCounter::create([
                        'user_id' => $request->userID,
                        'reward_id' => $data['rewardID'],
                        'point_status' => $data['rewardPoint'],
                        'created_at' => $this->dateTimeFormatTimeZone()
                    ]);
                }

                return response()->json([
                    'message' => 'api  $userPointCounter success.',
                ], 201);
            }

            return response()->json([
                'message' => 'api  $userPointCounter false.',
                'userID' => $request->userID,
                'userPoint' => $userPoint,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'api function userConfirmSelectReward error' . $e->getMessage()
            ]);
        }
    }
}
