<?php
namespace App\Models\Product\Lottery;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Lottery\Threed\Jisupailie3;


class IssueInfo extends Model
{
    protected $table = 'issueinfo';

    public static function oGetCurrentIssueForAward($sLottery)
    {
    	$sCurrentIssue = self::sGetLastDrawnIssue($sLottery);
    	return self::where("issue",$sCurrentIssue)->where('lottery',$sLottery)->where('award',0)->first();
    }

    public static function sGetLastDrawnIssue($sLottery)
    {
    	$sCurrentIssue = "";
    	switch ($sLottery) 
    	{
    		case 'jisupailie3':
    			$sCurrentIssue = Jisupailie3::sGetLastDrawnIssue();
    			break;
    	}
    	return $sCurrentIssue;
    }

    public static function oGetIssueInfoData($sLottery,$sIssue)
    {
        return self::where('lottery',$sLottery)->where('issue',$sIssue)->first();
    }

}