<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Product\Lottery\IssueInfoPushData;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Events\PushIssueInfoShanghaishrshrle;
use App\Events\Event;
use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\AwardProcessor;
use App\Models\Product\Lottery\Threed\ShanghaishrshrleAward;
use App\Models\Product\Lottery\Threed\ShanghaishrshrleFetchCode;

class CronShanghaishrshrle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronshanghaishrshrle';
    private $sGameType = "shanghaishrshrle";
    private $sStartTime = "10：30";
    private $sEndTime = "21：30";
    private static $sLottery = "shanghaishrshrle";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $sCurrentTime = date("H:i:s");
        if($sCurrentTime < $this->sStartTime || $sCurrentTime > $this->sEndTime)
        {
            return ;
        }
        $oDrawingIssueInfoPushData = IssueInfoPushData::oGetDrawingIssueInfoPushData(self::$sLottery);
        Redis::set('shanghaishrshrle', json_encode($oDrawingIssueInfoPushData));
        event(new PushIssueInfoShanghaishrshrle($oDrawingIssueInfoPushData));
        sleep(300);
        $oShanghaishrshrleFetchCode = new ShanghaishrshrleFetchCode();
        $oShanghaishrshrleFetchCode->vExecute();
        $oIssueInfoPushData = IssueInfoPushData::oGetLatestIssueInfoPushData(self::$sLottery,false);
        Redis::set('shanghaishrshrle', json_encode($oIssueInfoPushData));
        event(new PushIssueInfoShanghaishrshrle($oIssueInfoPushData));
        $oIssueInfo = IssueInfo::oGetCurrentIssueForAward(self::$sLottery);
        $oIssueInfo->vSetIssueInfoAwarded();
        $oShanghaishrshrleAward = new ShanghaishrshrleAward($oIssueInfo);
        $oAwardProcessor = new AwardProcessor($oShanghaishrshrleAward);
        $oAwardProcessor->handle();
    }
}
