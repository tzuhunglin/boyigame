<?php

namespace App\Models\Product\Card\Poke;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Card\Poke\BlackjackGameData;

class BlackjackRecord extends Model
{
	protected $table = 'blackjackrecord';
	const STATUS_WAITING = 0;
	const STATUS_GOING = 1;
	const STATUS_FINISHED = 4;

	public static function oGetUnfinishedRecord($iUserId)
	{
		return  self::where('userids','like', '%"' . $iUserId. '"%')->whereIn('status',array(self::STATUS_WAITING,self::STATUS_GOING));
	}

	public function oGetGameData()
	{
		return new BlackjackGameData($this);
	}

	public static function vUpdateByGameData($oGameData)
	{
		$oRecord = self::find($oGameData->iId);
		$oRecord->hashkey = $oGameData->sHashKey;
		$oRecord->status = $oGameData->iStatus;
		$oRecord->award = $oGameData->iAward;

		$oRecord->userids = json_encode(self::aGetStrIdList($oGameData->aUserIds));
		self::vRemoveUnnecessaryAttribute($oGameData);
		$oRecord->detail = json_encode($oGameData);
		$oRecord->save();
	}

	public static function aGetStrIdList($aIdList)
	{
		$iIdListLength = count($aIdList);
		for ($i=0; $i < $iIdListLength; $i++)
		{
			$aIdList[$i] = (String)$aIdList[$i];
		}

		return $aIdList;
	}

	public static function vRemoveUnnecessaryAttribute($oGameData)
	{
		unset($oGameData->iPlayUpdateTime);
		unset($oGameData->iTurn);
		unset($oGameData->iBetStartTime);
		unset($oGameData->sUpdatedAt);
		unset($oGameData->sCreatedAt);
		unset($oGameData->aCodes);
	}

	public static function aGetGameCardCodes($oGameData)
	{
		$aCardCodes = array();
		$aCardCodes[0] = $oGameData->aBankerInfo->aCards;
		$aUserCardCodes = self::aGetUserCardCodes($oGameData->aUserInfoList);
		$aCardCodes = array_merge($aCardCodes,$aUserCardCodes);
		return $aCardCodes;
	}

	public static function aGetUserCardCodes($aUserInfoList)
	{
		$iUserInfoListLength = count($aUserInfoList);
		$aUserCardCodes = array();
		for ($i=0; $i < $iUserInfoListLength; $i++)
		{
			array_push($aUserCardCodes, $aUserInfoList[$i]->aCards[0]);
		}
		return $aUserCardCodes;
	}

	public static function aGetRecordList($iUserId)
	{
		return  self::where('userids','like', '%"' . $iUserId. '"%')->where('status',self::STATUS_FINISHED)->get();
	}

	public static function oGetGameRecord($iId)
	{
		return self::find($iId);
	}
}