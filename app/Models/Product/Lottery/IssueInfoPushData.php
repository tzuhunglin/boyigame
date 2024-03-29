<?php
namespace App\Models\Product\Lottery;

use App\Models\Product\Lottery\IssueInfo;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Lottery\Threed\Shanghaishrshrle;


class IssueInfoPushData
{
	public $iId;
	public $sDateTime;
	public $sIssue;
	public $sCode;
	public $aCode;
	public $sGameType;
	public $sUpdateTime;

	private function __construct(IssueInfo $oIssueInfo)
	{
		$this->iId = $oIssueInfo->id;
		$this->sDateTime = $oIssueInfo->datetime;
		$this->sIssue = $oIssueInfo->issue;
		$this->sCode = $oIssueInfo->code;
		$this->aCode = json_decode($oIssueInfo->code);
		$this->sLottery = $oIssueInfo->lottery;
		$this->sUpdateTime = $oIssueInfo->updatetime;
	}

	public static function oGetLatestIssueInfoPushData($sLottery, $bCache = true)
	{
		if($bCache == true)
		{
			$oCacheIssueInfo = self::oGetLatestIssueInfoPushDataFromCache($sLottery);
			if($oCacheIssueInfo!=null)
			{
				return $oCacheIssueInfo;
			}
		}

		return self::oGetLatestIssueInfoPushDataFromDB($sLottery);
	}

	private static function oGetLatestIssueInfoPushDataFromDB($sLottery)
	{
		$oIssueInfo = IssueInfo::where('lottery',$sLottery)->orderBy('datetime', 'desc')->first();
        $oIssueInfoPushData = new self($oIssueInfo);
        Redis::set('shanghaishrshrle', json_encode($oIssueInfoPushData));
		return $oIssueInfoPushData;
	}

	private static function oGetLatestIssueInfoPushDataFromCache($sLottery)
	{
		$sIssueInfo = Redis::get($sLottery);
		if(empty($sIssueInfo))
		{
			return null;
		}
		return json_decode($sIssueInfo);
	}

	public static function oGetDrawingIssueInfoPushData($sLottery)
	{
		$oDrawingIssueInfoPushData = self::oGetLatestIssueInfoPushData($sLottery, $bCache = true);
		$oDrawingIssueInfoPushData->aCode = array("開","獎","中");
		$oDrawingIssueInfoPushData->sCode = json_encode($oDrawingIssueInfoPushData->aCode);
		$oDrawingIssueInfoPushData->sIssue = Shanghaishrshrle::sGetLastDrawnIssue();
		return $oDrawingIssueInfoPushData;

	}


}