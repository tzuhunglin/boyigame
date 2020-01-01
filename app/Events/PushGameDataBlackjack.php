<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Cache;

class PushGameDataBlackjack extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $oGameData;
    public $token;



    /**
     * Create a new event instance.
     *
     * @param User $oUser
     * @param      $oIssueInfo
     */
    public function __construct($oGameData)
    {
        $this->oGameData = $oGameData;
        $this->token = $oGameData->sHashKey;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['gameDataBlackjack'];
    }
}
