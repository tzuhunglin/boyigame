<?php

namespace App\Models\Product\Card\Poke;

use Illuminate\Foundation\Auth\User;
use App\Models\Product\Card\Poke;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Card\Poke\BlackjackRecord;


class BlackjackAward
{
	public $oRecord;
	public $oGameData;


	function __construct($sHashKey)
	{
		$this->oRecord = self::oGetRecord($sHashKey);
		$this->oGameData = json_decode($this->oRecord->detail);
	}

	private static function oGetRecord($sHashKey)
	{
		return BlackjackRecord::where('hashkey',$sHashKey)->first();
	}

	public function vHandle()
	{
		if(!empty($this->oGameData->aAwardInfo))
		{
			return;
		}
		$this->oGameData->aAwardInfo = array();
		$iUserInfoListLength = count($this->oGameData->aUserInfoList);
		for ($i=0; $i < $iUserInfoListLength; $i++)
		{
			User::find($oUserInfo->iUserId)->vFundDefrozen(500);
			$this->oGameData->aAwardInfo[$this->oGameData->aUserInfoList[$i]->iUserId] = 0;
			$this->vSetBetAward($this->oGameData->aUserInfoList[$i]);
			$this->vSetInsuranceAward($this->oGameData->aUserInfoList[$i]);
		}
		BlackjackRecord::vUpdateByGameData($this->oGameData);
	}

	public function vSetBetAward($oUserInfo)
	{
		$iBetMoney = (isset($oUserInfo->iDouble) && $oUserInfo->iDouble==3)? $oUserInfo->iBetAmount * 2 : $oUserInfo->iBetAmount;
		$oUser = User::find($oUserInfo->iUserId);
		$oUser->vReturnPointToParent($this->oGameData->sHashKey,$iBetMoney);
		$this->oGameData->aAwardInfo[$oUserInfo->iUserId] = array();

		if($oUserInfo->iWinLose==1)
		{
			$iWinMoney = $iBetAmount * ((90 + $oUser->keeppoint)/100);
			$oUser->availablemoney+=$iWinMoney;
			$oUser->totalmoney+=$iWinMoney;
			$this->oGameData->aAwardInfo[$oUserInfo->iUserId] += $iWinMoney;
		}
		else
		{
			$oUser->availablemoney-=$iBetMoney;
			$oUser->totalmoney-=$iBetMoney;
			$this->oGameData->aAwardInfo[$oUserInfo->iUserId] -= $iWinMoney;
		}
		$oUser->save();
	}

	public function vSetInsuranceAward($oUserInfo)
	{
		if(!isset($oUserInfo->iInsurance) || $oUserInfo->iInsurance!=3)
		{
			return;
		}
		$iInsuranceMoney = $oUserInfo->iBetAmount/2;
		$oUser->vReturnPointToParent($this->oGameData->sHashKey,$iInsuranceMoney);
		if(in_array(21,$oUserInfo->aBankerInfo->aPoints) && count($oUserInfo->aBankerInfo->aCards)==2)
		{
			$iWinMoney = $iInsuranceMoney * ((90 + $oUser->keeppoint)/100);
			$oUser->availablemoney+=$iWinMoney;
			$oUser->totalmoney+=$iWinMoney;
			$this->oGameData->aAwardInfo[$oUserInfo->iUserId] += $iWinMoney;
		}
		else
		{
			$oUser->availablemoney-=$iInsuranceMoney;
			$oUser->totalmoney-=$iInsuranceMoney;
			$this->oGameData->aAwardInfo[$oUserInfo->iUserId] -= $iInsuranceMoney;

		}
		$oUser->save();
	}



	public function oGetAwardInfo()
	{
		if(empty($this->oGameData->aAwardInfo))
		{
			$this->vHandle();
		}

		return $this->oGameData->aAwardInfo;
	}
}