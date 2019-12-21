<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Product\Lottery\IssueInfoPushData;
use Cache;

class PushIssueInfoJisupailie3 extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $oIssueInfoPushData;


    /**
     * Create a new event instance.
     *
     * @param User $oUser
     * @param      $oIssueInfo
     */
    public function __construct(IssueInfoPushData $oIssueInfoPushData)
    {
        $this->oIssueInfoPushData = $oIssueInfoPushData;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['issueInfoJisupailie3'];
    }
}
