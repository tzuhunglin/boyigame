<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Product\Lottery\IssueInfoPushData;
use Cache;
use App\Events\PushIssueInfoShanghaishrshrle;
use App\Events\Event;


class CronShanghaishrshrle extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $sGameType = "shanghaishrshrle";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $oIssueInfoPushData = IssueInfoPushData::oGetLatestIssueInfoPushData($this->sGameType,false);
        Cache::put('issueInfoShanghaishrshrle',json_encode($oIssueInfoPushData),1000);
        event(new PushIssueInfoShanghaishrshrle($oIssueInfoPushData));
    }
}
