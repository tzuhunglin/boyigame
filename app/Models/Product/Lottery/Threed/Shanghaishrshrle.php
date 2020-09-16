<?php

namespace App\Models\Product\Lottery\Threed;

use Illuminate\Foundation\Auth\User;
use App\Models\Product;
use App\Models\Product\BetOrder;


class Shanghaishrshrle extends Product
{
    public static $sLottery = 'shanghaishrshrle';
    public $aBetOrder = array();
    private $aCodeSetList = array();
    private $aBetOrderList = array();
    private $sProductType = "lottery";
    private static $sIssueStartTime = "10:30";
    private static $sIssueEndTime = "21:30";
    private static $iIssueInterval = 1800;
    public $iTotalBetMoney = 0;



    function __construct($aData, User $oUser)
    {
        $this->sType = $aData['gametype'];
        $this->iMultiple = $aData['multiple'];
        $this->iReturn = $aData['return'];
        $this->aCode = $aData['codes'];
        $this->oUser = $oUser;
    }

    private static $iCodeRangeMin = 0;
    private static $iCodeRangeMax = 9;
    private static $iCodeAmountMax = 10;


    public static function aGetAllGameType()
    {
        return ['sanmajrshiuan','sanmatzushiuan','yimabudingdan'];
    }

    public static function aGetAllGameTypeOdds()
    {
        return  [
                    'sanmajrshiuan' => 1000,
                    'sanmatzushiuan' => 166.66,
                    'yimabudingdan' => 3.69 
                ];
    }

    public static function fGetGameTypeOdds($sType)
    {
        return self::aGetAllGameTypeOdds()[$sType];
    }

    public static function sGetGameTypeOdds($sGameType)
    {
        return self::aGetAllGameTypeOdds()[$sGameType];
    }

    public static function aBetCheck($aInput,$iAvailableMoney)
    {
        if( (date("H:i:s") > "10:29:55" && date("H:i:s") < "21:31:00")  && (((date("i:s") > "29:55" && date("i:s") < "30:55"))||((date("i:s") > "59:55" && date("i:s") < "00:55"))))
        {
            return ['status'=>false,'message'=>'投注時間已過，請等下期投注'];
        }

        if(empty($aInput['gametype'])||!in_array($aInput['gametype'],self::aGetAllGameType()))
        {
            return ['status'=>false,'message'=>'玩法錯誤'];
        }

        if(empty($aInput['multiple'])||!is_numeric($aInput['multiple'])||$aInput['multiple']<1)
        {
            return ['status'=>false,'message'=>'倍數錯誤'];
        }

        if(!in_array(intval($aInput['return']),array(0,1)))
        {
            return ['status'=>false,'message'=>'返點錯誤'];
        }

        if(empty($aInput['codes'])||!self::bCodesCheck($aInput['gametype'],$aInput['codes']))
        {
            return ['status'=>false,'message'=>'號碼錯誤'];
        }

        if(($aInput['multiple']*self::BASIC_AMOUNT)>$iAvailableMoney)
        {
            return ['status'=>false,'message'=>'投注金額超過可用餘額'];
        }

        return ['status'=>true];

    }

    private static function bCodesCheck($sGameType,$aCodes)
    {
        if(empty($aCodes))
        {
            return false;
        }
        $aCodeCheckPara = self::aGetCodeCheckPara($sGameType);
        $iCodeCheckParaLength = count($aCodeCheckPara);


        if(count($aCodes)!=$iCodeCheckParaLength)
        {
            return false;
        }

        for ($i=0; $i < $iCodeCheckParaLength ; $i++)
        {
            if(count($aCodes[$i])<$aCodeCheckPara[$i] || count($aCodes[$i])>self::$iCodeAmountMax)
            {
                return false;
            }

            foreach ($aCodes[$i] as $iNumber)
            {
                if(!is_numeric($iNumber)||$iNumber<self::$iCodeRangeMin||$iNumber>self::$iCodeRangeMax)
                {
                    return false;
                }
            }
        }

        return true;
    }

    private static function aGetCodeCheckPara($sGameType)
    {
        $aCodeCheckPara = array();
        switch ($sGameType) {
            case 'sanmajrshiuan':
                $aCodeCheckPara = [1,1,1];
                break;
            case 'sanmatzushiuan':
                $aCodeCheckPara = [3];
                break;
            case 'yimabudingdan':
                $aCodeCheckPara = [1];
                break;
        }
        return $aCodeCheckPara;
    }

    public function vBetPrepare()
    {

    }

    public function vTotalBetMoneyCalculate()
    {
        $iTotalCodeSet = count($this->aCodeSetList);
        $this->iTotalBetMoney = $iTotalCodeSet * self::BASIC_AMOUNT;
    }

    public function vBet()
    {
        foreach ($this->aBetOrderList as $oBetOrder)
        {
            $this->oUser->vSetDeductMoneyAmount($oBetOrder->iBetMoney);
            $oBetOrder->vSave();
            if($this->iReturn==true)
            {
                $this->oUser->vReturnPointToSelf($oBetOrder->id,$oBetOrder->iBetMoney,self::$sLottery);
            }
            $this->oUser->vReturnPointToParent($oBetOrder->id,$oBetOrder->iBetMoney,self::$sLottery,$this->oUser->id);
        }
        $this->oUser->vSetMoneyDeduct();
    }

    public function vBetOrderBuild()
    {
        $this->vSetIssue();
        foreach ($this->aCodeSetList as $aCodeSetData)
        {
            $oBetOrder = new BetOrder($this->oUser, self::BASIC_AMOUNT, self::BASIC_ODDS);
            $oBetOrder->sLottery = self::$sLottery;
            $oBetOrder->sType = $this->sType;
            $oBetOrder->iMultiple = $this->iMultiple;
            $oBetOrder->iReturn = $this->iReturn;
            $oBetOrder->iUserId = $this->oUser->id;
            $oBetOrder->sIssue = $this->sIssue;
            $oBetOrder->sCode = json_encode($aCodeSetData);
            $oBetOrder->vSetBetMoney();
            $oBetOrder->vSetRealBetMoney();
            array_push($this->aBetOrderList,$oBetOrder);
        }
    }


    public function vCodeSetBuild()
    {
        switch ($this->sType) {
            case 'sanmajrshiuan':
                $this->vSanmajrshiuanCodeSetBuild();
                break;
            case 'sanmatzushiuan':
                $this->vSanmatzushiuanCodeSetBuild();
                break;
            case 'yimabudingdan':
                $this->vYimabudingdanCodeSetBuild();
                break;
        }
    }

    private function vSanmatzushiuanCodeSetBuild()
    {
        sort($this->aCode[0]);
        $iCodeCount = count($this->aCode[0]);
        $i = 0;
        while($i<$iCodeCount-2)
        {
            $j = $i+1;
            while($j<$iCodeCount-1)
            {
                $k=$j+1;
                while($k<$iCodeCount)
                {
                    array_push($this->aCodeSetList,array((String)$this->aCode[0][$i],(String)$this->aCode[0][$j],(String)$this->aCode[0][$k]));
                    $k++;
                }
                $j++;
            }
            $i++;
        }
    }

    private function vYimabudingdanCodeSetBuild()
    {
        foreach ($this->aCode[0] as $iCode0)
        {
            array_push($this->aCodeSetList,array($iCode0));
        }
    }

    private function vSanmajrshiuanCodeSetBuild()
    {

        foreach ($this->aCode[0] as $iCode0)
        {
            foreach ($this->aCode[1] as $iCode1)
            {
                foreach ($this->aCode[2] as $iCode2)
                {
                    array_push($this->aCodeSetList,array($iCode0,$iCode1,$iCode2));
                }
            }
        }
    }

    private function vSetIssue()
    {
        $this->sIssue = self::sGetComingIssue();
    }

    public static function sGetLastDrawnIssue()
    {
        $sCurrentTime = date("H:i");
        if($sCurrentTime>self::$sIssueEndTime)
        {
            $sDate = date('Ymd');
            $sIssue = "23";
        }
        elseif($sCurrentTime<self::$sIssueStartTime)
        {
            $sDate = date('Ymd', strtotime('-1 day'));
            $sIssue = "23";
        }
        else
        {
            $sDate = date('Ymd');
            $iIssueStartTimeStamp = strtotime(date('Ymd').self::$sIssueStartTime);
            $iTimeStampDiff = time() - $iIssueStartTimeStamp;
            $iIssue = floor($iTimeStampDiff/self::$iIssueInterval) + 1;
            $sIssue = ($iIssue<10)? sprintf("0%s",$iIssue) : $iIssue;
        }

        return sprintf("%s-%s",$sDate,$sIssue);
    }

    public static function sGetComingIssue()
    {
        $sCurrentTime = date("H:i");
        if($sCurrentTime>self::$sIssueEndTime)
        {
            $sDate = date('Ymd', strtotime('+1 day'));
            $sIssue = "01";
        }
        elseif($sCurrentTime<self::$sIssueStartTime)
        {
            $sDate = date('Ymd');
            $sIssue = "01";
        }
        else
        {
            $sDate = date('Ymd');
            $iIssueStartTimeStamp = strtotime(date('Ymd').self::$sIssueStartTime);
            $iTimeStampDiff = time() - $iIssueStartTimeStamp;
            $iIssue = floor($iTimeStampDiff/self::$iIssueInterval) + 2;
            $sIssue = ($iIssue<10)? sprintf("0%s",$iIssue) : $iIssue;
        }

        return sprintf("%s-%s",$sDate,$sIssue);
    }
}
