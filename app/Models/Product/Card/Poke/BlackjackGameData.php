<?php

namespace App\Models\Product\Card\Poke;

use Illuminate\Foundation\Auth\User;



class BlackjackGameData
{
	public $iId;
	public $sHashKey;
	public $iStatus;
	public $iStage;
	public $aUserIds;
	public $aUserList;
	public $aCodes;
	public $sCreatedAt;
	public $sUpdatedAt;

	function __construct($mData)
	{

		(is_array($mData))?$this->vSetAttributeByArray($mData):$this->vSetAttributeByObject($mData);
		$this->vSetUserList();
	}

	private function vSetUserList()
	{
		$this->aUserList = array();
		foreach ($this->aUserIds as $iUserId)
		{
			$this->aUserList = self::aGetListWithNewUser($this->aUserList,$iUserId);
		}
	}

	public static function aGetListWithNewUser($aUserList,$iUserId)
	{
		$oUserData = User::find($iUserId);
		$aUserData = array(
						"iUserId" => $oUserData->id,
						"sUserName" => $oUserData->name,
						"iAvailableMoney" => $oUserData->availablemoney,
						"iBetAmount" => 0
		);
		array_push($aUserList,$aUserData);
		return $aUserList;
	}

	private function vSetAttributeByArray($aData)
	{
		$this->iId = $aData['iId'];
		$this->sHashKey = $aData['sHashKey'];
		$this->iStatus = $aData['iStatus'];
		$this->iStage = $aData['iStage'];
		$this->aUserIds = $aData['aUserIds'];
		$this->aCodes = $aData['aCodes'];
		$this->sCreatedAt = $aData['sCreatedAt'];
		$this->sUpdatedAt = $aData['sUpdatedAt'];
	}

	private function vSetAttributeByObject($oData)
	{
		$this->iId = $oData->id;
		$this->sHashKey = $oData->hashkey;
		$this->iStatus = $oData->status;
		$this->iStage = $oData->stage;
		$this->aUserIds = json_decode($oData->userids,true);
		$this->aCodes = json_decode($oData->codes,true);
		$this->sCreatedAt = $oData->created_at;
		$this->sUpdatedAt = $oData->updated_at;
	}
}