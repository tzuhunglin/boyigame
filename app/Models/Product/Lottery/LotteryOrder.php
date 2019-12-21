<?php
namespace App\Models\Product\Lottery;

use Illuminate\Database\Eloquent\Model;


class LotteryOrder extends Model
{
    protected $table = 'lotteryorder';

    public function vBonusUpdate()
    {
    	$this->win = 1;
    	$this->award = 1;
    	$this->save();
    }
}