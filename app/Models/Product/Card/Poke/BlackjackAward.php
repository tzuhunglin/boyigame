<?php

namespace App\Models\Product\Card\Poke;

use App\Models\User;
use App\Models\Product\Card\Poke;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Card\Poke\BlackjackRecord;


class BlackjackAward
{
	public $oRecord;
	public $oGameData;
	public $aUserList = array();


	function __construct($sHashKey)
	{
		$this->oRecord = self::oGetRecord($sHashKey);
		$this->oGameData = (!empty($this->oRecord->detail))?json_decode($this->oRecord->detail):self::oGetGameData($sHashKey);
	}

	private static function oGetGameData($sHashKey)
	{
		return Blackjack::oGetGameData($sHashKey);
	}

	private static function oGetRecord($sHashKey)
	{
		return BlackjackRecord::where('hashkey',$sHashKey)->first();
	}

	public function vHandle()
	{
		if($this->oRecord->award!=0)
		{
			return;
		}
		$this->oGameData->aAwardInfo = array();
		$iUserInfoListLength = count($this->oGameData->aUserInfoList);
		for ($i=0; $i < $iUserInfoListLength; $i++)
		{
			$iUserId = $this->oGameData->aUserInfoList[$i]->iUserId;
			$this->aUserList[$iUserId] = User::find($iUserId);
			$oUser = $this->aUserList[$iUserId];
			$oUser->vFundDefrozens(500);
			$this->oGameData->aUserInfoList[$i]->iSumUp = 0;
			$this->vSetBetAward($this->oGameData->aUserInfoList[$i]);
			$this->vSetInsuranceAward($this->oGameData->aUserInfoList[$i]);
		}
		$this->oGameData->iAward = 1;
		BlackjackRecord::vUpdateByGameData($this->oGameData);
	}

	public function vSetInsuranceAward($oUserInfo)
	{
		if(!isset($oUserInfo->iInsurance) || $oUserInfo->iInsurance!=3)
		{
			return;
		}
		$oUser = $this->aUserList[$oUserInfo->iUserId];
		$iInsuranceMoney = $oUserInfo->iBetAmount/2;
		$oUser->vReturnPointToParent($this->oGameData->sHashKey,$iInsuranceMoney);
		if(in_array(21,$this->oGameData->aBankerInfo->aPoints) && count($this->oGameData->aBankerInfo->aCards)==2)
		{
			$iWinMoney = $iInsuranceMoney * ((90 + $oUser->keeppoint)/100);
			$oUser->availablemoney += $iWinMoney;
			$oUser->totalmoney += $iWinMoney;
			$oUserInfo->iSumUp += $iWinMoney;
		}
		else
		{
			$oUser->availablemoney-=$iInsuranceMoney;
			$oUser->totalmoney -= $iInsuranceMoney;
			$oUserInfo->iSumUp -= $iInsuranceMoney;

		}
		$oUser->save();
	}

	public function vSetBetAward($oUserInfo)
	{
		$iBetMoney = (isset($oUserInfo->iDouble) && $oUserInfo->iDouble==3)? $oUserInfo->iBetAmount * 2 : $oUserInfo->iBetAmount;
		$oUser = $this->aUserList[$oUserInfo->iUserId];
		$oUser->vReturnPointToParent($this->oGameData->sHashKey,$iBetMoney);

		if($oUserInfo->iWinLose==1)
		{
			$iWinMoney = $iBetMoney * ((90 + $oUser->keeppoint)/100);
			$oUser->availablemoney+=$iWinMoney;
			$oUser->totalmoney += $iWinMoney;
			$oUserInfo->iSumUp += $iWinMoney;
		}
		else
		{
			$oUser->availablemoney-=$iBetMoney;
			$oUser->totalmoney -= $iBetMoney;
			$oUserInfo->iSumUp -= $iBetMoney;
		}
		$oUser->save();
	}



	public function oGetAwardedGameData()
	{
		if($this->oRecord->award==0)
		{
			$this->vHandle();
		}
		return $this->oGameData;
	}
}