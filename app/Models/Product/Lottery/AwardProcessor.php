<?php

namespace App\Models\Product\Lottery;

use App\Models\Product\Bet ;
use DB;
use App\Models\Product;
use App\Models\User;


class AwardProcessor 
{

    function __construct($oLotteryAward)
    {
        $this->oLotteryAward = $oLotteryAward;
    }

    public function handle()
    {
        foreach ($this->oLotteryAward->aGameType as $sType) 
        {
            $this->oLotteryAward->vSendAward($sType);
        }
    }
}