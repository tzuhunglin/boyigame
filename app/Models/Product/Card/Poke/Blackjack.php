<?php

namespace App\Models\Product\Card\Poke;

use Illuminate\Foundation\Auth\User;
use App\Models\Product\Card\Poke;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Card\Poke\BlackjackRecord;


class Blackjack extends Poke
{
	public $iUserId;
	public $oUnfinishedGameData;

	function __construct($iUserId)
	{
		$this->iUserId = $iUserId;
		$this->vSetUnfinishedGameData();
	}

	public static function oGetUserGameData($iUserId)
	{
		$sBlackjackUserKey = sprintf("blackjack_%s",$iUserId);
        $sHashKey = json_decode(Redis::get($sBlackjackUserKey));
        if(empty($sHashKey))
        {
        	return null;
        }
        return self::oGetGameData($sHashKey);
	}

	public static function oGetGameData($sHashKey)
	{
		$sGameData = Redis::get($sHashKey);
		if(empty($sGameData))
		{
			return null;
		}
		return json_decode($sGameData);
	}

	private function vSetUnfinishedGameData()
	{
		$oGameData = self::oGetUserGameData($this->iUserId);
        if(empty($oGameData))
        {
            $oGameData = $this->oGetPriorWaitingGameData();
        }

        if(empty($oGameData))
        {
            $oGameData = $this->oGetNewGameData();
        }

        $this->oUnfinishedGameData = $oGameData;
	}

	private function oGetNewGameData()
	{
		$oRecord = new BlackjackRecord;
		$oRecord->hashkey = md5($this->iUserId.time());
		$oRecord->status = 0;
		$oRecord->stage = 0;
		$oRecord->userids = json_encode(array($this->iUserId));
		$oRecord->codes = json_encode(array());
		$oRecord->save();
		$oGameData = $oRecord->oGetGameData();
		self::vAddWaitingGame($oGameData->sHashKey);
		self::vSetUserGameHashKey($this->iUserId,$oGameData->sHashKey);
		self::vSetGameData($oGameData);
		return $oGameData;
	}

	private static function vAddWaitingGame($sHashKey)
	{
		$aWaitingGameList = self::aGetWaitingGameList();
		$aWaitingGameList = (empty($aWaitingGameList))?array():$aWaitingGameList;
		array_push($aWaitingGameList, $sHashKey);
		self::vSetWaitingGameList($aWaitingGameList);
	}

	private static function vRemoveWaitingGame($sHashKey)
	{
		$aWaitingGameList = self::aGetWaitingGameList();
		$iIndex = array_search($sHashKey,$aWaitingGameList,true);
		unset($aWaitingGameList[$iIndex]);
		$aWaitingGameList = array_filter(array_values($aWaitingGameList));
		self::vSetWaitingGameList($aWaitingGameList);
	}

	public static function aGetWaitingGameList()
	{
		$sRedisKey = "blackjack_waitinggamelist";
        $aGameList = json_decode(Redis::get($sRedisKey),true);
        return $aGameList;
	}

	public static function vSetWaitingGameList($aGameList)
	{
		Redis::set("blackjack_waitinggamelist",json_encode($aGameList));
	}

	public static function vSetUserGameHashKey($iUserId,$sHashKey)
	{
		$sRedisKey = sprintf("blackjack_%s",$iUserId);
        Redis::set($sRedisKey,json_encode($sHashKey));
	}

	private static function vSetGameData($oGameData)
	{
		Redis::set($oGameData->sHashKey,json_encode($oGameData));
	}

	private function oGetPriorWaitingGameData()
	{
		$aWaitingGameList = self::aGetWaitingGameList();

		if(empty($aWaitingGameList))
		{
			return null;
		}
		$sHashKey = array_shift($aWaitingGameList);
		$oWaitingGameData = self::oGetGameData($sHashKey);
		if(empty($oWaitingGameData))
		{
			return null;
		}
		array_push($oWaitingGameData->aUserIds,$this->iUserId);
		$oWaitingGameData->aUserInfoList = BlackjackGameData::aGetListWithNewUser($oWaitingGameData->aUserInfoList,$this->iUserId);
		self::vSetUserGameHashKey($this->iUserId,$oWaitingGameData->sHashKey);
		self::vSetGameData($oWaitingGameData);
		return $oWaitingGameData;
	}
}