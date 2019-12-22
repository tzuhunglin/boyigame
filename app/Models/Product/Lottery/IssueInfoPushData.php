<?php
namespace App\Models\Product\Lottery;

use App\Models\Product\Lottery\IssueInfo;
use Cache;
use App\Models\Product\Lottery\Threed\Jisupailie3;


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
		return new self($oIssueInfo);
	} 

	private static function oGetLatestIssueInfoPushDataFromCache($sLottery)
	{
		$sIssueInfo = Cache::get($sLottery);
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
		$oDrawingIssueInfoPushData->sIssue = Jisupailie3::sGetLastDrawnIssue();
		return $oDrawingIssueInfoPushData;

	}


}