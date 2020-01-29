<?php

namespace App\Models\Product\Lottery\Threed;

use Illuminate\Foundation\Auth\User;
use App\Models\Product;
use App\Models\Product\BetOrder;
use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\Threed\Shanghaishrshrle;
use App\Models\Product\Lottery\LotteryOrder;


class ShanghaishrshrleAward
{
    private static $sLottery = 'shanghaishrshrle';

    private $oIssueInfo;
    private $aUserList;
    private $aUserAwardMoneyList;

    function __construct($oIssueInfo)
    {
        $this->oIssueInfo = $oIssueInfo;
        $this->aGameType = self::aGetAllGameType();
    }

    private function aGetAllGameType()
    {
        return Shanghaishrshrle::aGetAllGameType();
    }

    public function vSendAward($sType)
    {
        $iOdds = Shanghaishrshrle::fGetGameTypeOdds($sType);
        $aAwardBetOrderList = $this->aGetAwardBetOrderList($sType);
        $this->vCalculateUserAwardMoney($aAwardBetOrderList,$iOdds);
        $this->vSetUserAwardMoney();
        $this->vSetBetOrderAwarded($sType);
        $this->aUserAwardMoneyList = array();
    }

    private function aGetAwardBetOrderList($sType)
    {
        $aAwardBetOrder = array();
        switch ($sType) {
            case 'sanmajrshiuan':
                $aAwardBetOrder = $this->vGetAwardSanmajrshiuanBetOrder($sType);
                break;
            case 'sanmatzushiuan':
                $aAwardBetOrder = $this->vGetAwardSanmatzushiuanBetOrder($sType);
                break;
            case 'yimabudingdan':
                $aAwardBetOrder = $this->vGetAwardYimabudingdanBetOrder($sType);
                break;
        }
        return $aAwardBetOrder;
    }

    private function vSetBetOrderAwarded($sType)
    {
        LotteryOrder::where("lottery",self::$sLottery)
                    ->where("type",$sType)
                    ->where("issue",$this->oIssueInfo->issue)
                    ->where("award",0)
                    ->update(['award' => 1]);
    }

    private function vGetAwardSanmajrshiuanBetOrder($sType)
    {
        return LotteryOrder::where("lottery",self::$sLottery)
                            ->where("type",$sType)
                            ->where("issue",$this->oIssueInfo->issue)
                            ->where("award",0)
                            ->where("code",str_replace(" ","",$this->oIssueInfo->code))
                            ->get();
    }

    private function vGetAwardYimabudingdanBetOrder($sType)
    {
        $aCode = json_decode($this->oIssueInfo->code);
        for ($i=0; $i < count($aCode); $i++)
        {
            $aCode[$i] = json_encode(array($aCode[$i]));
        }
        return LotteryOrder::where("lottery",self::$sLottery)
                            ->where("type",$sType)
                            ->where("issue",$this->oIssueInfo->issue)
                            ->where("award",0)
                            ->where(function($query) use ($aCode) {
                              $query->where('code', $aCode[0])
                                ->orWhere('code', $aCode[1])
                                ->orWhere('code', $aCode[2]);
                            })
                            ->get();
    }

    private function vGetAwardSanmatzushiuanBetOrder($sType)
    {
        $aCode = json_decode($this->oIssueInfo->code);
        sort($aCode);

        return LotteryOrder::where("lottery",self::$sLottery)
                            ->where("type",$sType)
                            ->where("issue",$this->oIssueInfo->issue)
                            ->where("award",0)
                            ->where("code",json_encode($aCode))
                            ->get();

    }

    private function vSendAwardSanmajrshiuan($iOdds,$sType)
    {
        $aWinBetOrderList = LotteryOrder::where("lottery",self::$sLottery)
                                        ->where("type",$sType)
                                        ->where("issue",$oIssueInfo->issue)
                                        ->where("award",0)
                                        ->where("code",$oIssueInfo->code)
                                        ->get();
    }

    private function vCalculateUserAwardMoney($aAwardBetOrderList,$iOdds)
    {
        $this->aUserAwardMoneyList = array();
        foreach ($aAwardBetOrderList as $oAwardBetOrder)
        {
            if(!isset($this->aUserAwardMoneyList[$oAwardBetOrder->userid]))
            {
                $this->aUserAwardMoneyList[$oAwardBetOrder->userid] = 0;
            }
            $oAwardBetOrder->win = 1;
            $oAwardBetOrder->award = 1;
            $oAwardBetOrder->save();
            $iAwardMoney = $oAwardBetOrder->realbetmoney * $iOdds;
            $this->aUserAwardMoneyList[$oAwardBetOrder->userid] += $iAwardMoney;
        }
    }

    private function vSetUserAwardMoney()
    {
        foreach ($this->aUserAwardMoneyList as $iUserId => $iAwardMoney)
        {
            $oUser = User::find($iUserId);
            $oUser->totalmoney += $iAwardMoney;
            $oUser->availablemoney += $iAwardMoney;
            $oUser->save();
        }
    }
}