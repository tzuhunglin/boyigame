<?php

namespace App\Http\Controllers\Product\Lottery\Threed;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Lottery\Threed\Shanghaishrshrle;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Lottery\IssueInfoPushData;
use App\Models\Product\Lottery\LotteryBet;
use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\AwardProcessor;
use App\Models\Product\Lottery\Threed\ShanghaishrshrleAward;

class ShanghaishrshrleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $oShanghaishrshrle = IssueInfoPushData::oGetLatestIssueInfoPushData('shanghaishrshrle');
        $oUser = Auth::user();
        return view('product.lottery.threed.shanghaishrshrle.index',[
                    'oUser' => $oUser,
                    'iBasicOdds' => Shanghaishrshrle::BASIC_ODDS,
                    'aAllGameTypeOdds' => Shanghaishrshrle::aGetAllGameTypeOdds(),
                    'oShanghaishrshrle'=> $oShanghaishrshrle,
                    'sAppUrl' => env('APP_URL')
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


        $aBetCheck = Shanghaishrshrle::aBetCheck($aInput,$oUser->availablemoney);
        if($aBetCheck['status']!=true)
        {
            return $aBetCheck;
        }
        $oShanghaishrshrle = new Shanghaishrshrle($aInput,$oUser);

        $oShanghaishrshrle->vCodeSetBuild();
        $oShanghaishrshrle->vTotalBetMoneyCalculate();

        if(!$oUser->bIsAvailableMoneyEnough($oShanghaishrshrle->iTotalBetMoney))
        {
            return ['status'=> false, 'message'=> '可用餘額不足'];
        }

        $oShanghaishrshrle->vBetOrderBuild();

        $oShanghaishrshrle->vBet();

        return array("status"=>true,"message"=>"投注成功");
    }

    public function awardTest()
    {
        $oIssueInfo = IssueInfo::oGetCurrentIssueForAward("shanghaishrshrle");
        $oIssueInfo->vSetIssueInfoAwarded();
        $oShanghaishrshrleAward = new ShanghaishrshrleAward($oIssueInfo);
        $oAwardProcessor = new AwardProcessor($oShanghaishrshrleAward);
        $oAwardProcessor->handle();
    }
}
