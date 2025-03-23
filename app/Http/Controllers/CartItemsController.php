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

    public function userConfirmSelectReward (Request $request) {
        try {

            $userPoint = new UserPoint();
            $userPointCounter = new UserPointCounter();
            $reward = new Reward();

            $request->validate([
                'userID' => 'required|integer',
                'totalPoint' => 'required|integer'
            ]);

            $counterItems = $request->input('counterItems');

            foreach (json_decode($counterItems) as $item) {
                $data = [
                    'rewardID' => $item->rewardID,
                    'rewardName' => $item->rewardName,
                    'reward'
                ];
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'api function userConfirmSelectReward error' . $e->getMessage()
            ]);
        }
    }
}
