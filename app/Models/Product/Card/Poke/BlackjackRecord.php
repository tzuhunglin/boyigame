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
	const STATUS_FINISHED = 2;

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
		$oRecord->stage = $oGameData->iStage;
		$oRecord->userids = json_encode($oGameData->aUserIds);
		$oRecord->codes = json_encode($oGameData->aCodes);
		$oRecord->save();
	}
}