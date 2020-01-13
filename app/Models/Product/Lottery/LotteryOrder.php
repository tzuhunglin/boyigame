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

    public static function aGetBetRecordList($iUserId)
    {
    	return self::where('userid',$iUserId)->get();
    }

    public static function oGetBetRecordData($iId)
    {
    	return self::find($iId);
    }
}