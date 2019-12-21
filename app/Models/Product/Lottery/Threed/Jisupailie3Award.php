<?php

namespace App\Models\Product\Lottery\Threed;

use Illuminate\Foundation\Auth\User;
use App\Models\Product;
use App\Models\Product\BetOrder;
use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\Threed\Jisupailie3;
use App\Models\Product\Lottery\LotteryOrder;


class Jisupailie3Award
{
    private static $sLottery = 'jisupailie3';

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
        return Jisupailie3::aGetAllGameType();
    }

    public function vSendAward($sType)
    {
        $iOdds = Jisupailie3::fGetGameTypeOdds($sType);
        $aAwardBetOrderList = $this->aGetAwardBetOrderList($sType);

        $this->vCalculateUserAwardMoney($aAwardBetOrderList,$iOdds);
        $this->vSetUserAwardMoney();
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
        return LotteryOrder::where("lottery",self::$sLottery)
                            ->where("type",$sType)
                            ->where("issue",$this->oIssueInfo->issue)
                            ->where("award",0)
                            ->where("code",json_decode($this->oIssueInfo->code)[0])
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