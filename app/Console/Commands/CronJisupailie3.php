<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Product\Lottery\IssueInfoPushData;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Events\PushIssueInfoJisupailie3;
use App\Events\Event;

use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\AwardProcessor;
use App\Models\Product\Lottery\Threed\Jisupailie3Award;


class CronJisupailie3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjisupailie3';
    private $sGameType = "jisupailie3";
    private $sStartTime = "10：30";
    private $sEndTime = "21：30";
    private static $sLottery = "jisupailie3";

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
        Redis::set('jisupailie3', json_encode($oDrawingIssueInfoPushData));
        event(new PushIssueInfoJisupailie3($oDrawingIssueInfoPushData));
        sleep(300);
        $oIssueInfoPushData = IssueInfoPushData::oGetLatestIssueInfoPushData(self::$sLottery,false);
        Redis::set('jisupailie3', json_encode($oIssueInfoPushData));
        event(new PushIssueInfoJisupailie3($oIssueInfoPushData));
        $oIssueInfo = IssueInfo::oGetCurrentIssueForAward(self::$sLottery);
        $oJisupailie3Award = new Jisupailie3Award($oIssueInfo);
        $oAwardProcessor = new AwardProcessor($oJisupailie3Award);
        $oAwardProcessor->handle();
    }
}
