<?php
namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use App\Models\Product\Lottery\LotteryOrder;


class BetOrder 
{
	public $oUser = false;
	public $iBasicAmount = false;
	public $iBasicOdds = false;
    public $sLottery = false;

    function __construct(User $oUser, $iBasicAmount, $iBasicOdds)
    {
    	$this->oUser = $oUser;
    	$this->iBasicAmount = $iBasicAmount;
    	$this->iBasicOdds = $iBasicOdds;
    }


    public function vSetBetMoney()
    {
        $this->iBetMoney = $this->iBasicAmount * $this->iMultiple;
    }

    public function vSetRealBetMoney()
    {
    	$this->vSetBetMoney();
    	$iOdds = $this->iBasicOdds;
    	if($this->iReturn==0)
    	{
			$iOdds += $this->oUser->keeppoint;
    	}

    	$this->iRealBetMoney = $this->iBetMoney * ($iOdds/100);
    }

    public function vSave()
    {
        $oLotteryOrder = new LotteryOrder;
        $oLotteryOrder->lottery = $this->sLottery;
        $oLotteryOrder->type = $this->sType;
        $oLotteryOrder->multiple = $this->iMultiple;
        $oLotteryOrder->return = $this->iReturn;
        $oLotteryOrder->userid = $this->oUser->id;
        $oLotteryOrder->issue = $this->sIssue;
        $oLotteryOrder->code = $this->sCode;
        $oLotteryOrder->betmoney = $this->iBetMoney;
        $oLotteryOrder->realbetmoney = $this->iRealBetMoney;
        $oLotteryOrder->save();
        $this->id = $oLotteryOrder->id;
    }
}