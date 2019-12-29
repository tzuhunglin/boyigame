<?php

namespace App\Http\Controllers\Product\Lottery\Threed;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Lottery\Threed\Jisupailie3;
use Cache;
use Redis;
use App\Models\Product\Lottery\IssueInfoPushData;
use App\Models\Product\Lottery\LotteryBet;

class Jisupailie3Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Redis::set('name', 'Taylor');
        // $user = Redis::get('name');
        // echo "<pre>"; print_r($user );exit;
        $oJisupailie3 = IssueInfoPushData::oGetLatestIssueInfoPushData('jisupailie3');
        $oUser = Auth::user();
        return view('product.lottery.threed.jisupailie3.index',[
                    'oUser' => $oUser,
                    'iBasicOdds' => Jisupailie3::BASIC_ODDS,
                    'aAllGameTypeOdds' => Jisupailie3::aGetAllGameTypeOdds(),
                    'oJisupailie3'=> $oJisupailie3
        ]);
    }

    public function bet(Request $oRequest)
    {
        $aInput = $oRequest->all();
        $oUser = Auth::user();
        if($oUser->bIsTopUser())
        {
            return ['status'=> false, 'message'=> '總代不能投注，請使用下級帳號．'];
        }


        $aBetCheck = Jisupailie3::aBetCheck($aInput);
        if($aBetCheck['status']!=true)
        {
            return $aBetCheck;
        }
        $oJisupailie3 = new Jisupailie3($aInput,$oUser);  

        $oJisupailie3->vCodeSetBuild();
        $oJisupailie3->vTotalBetMoneyCalculate();

        if(!$oUser->bIsAvailableMoneyEnough($oJisupailie3->iTotalBetMoney))
        {
            return ['status'=> false, 'message'=> '可用餘額不足'];
        }

        $oJisupailie3->vBetOrderBuild();

        $oJisupailie3->vBet();

        return array("status"=>true,"message"=>"投注成功");
    }
}
