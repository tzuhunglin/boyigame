<?php
namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;


class BetReturn extends Model
{
    protected $table = 'betreturn';

    public static function vSetBetReturn($iBetOrderId, $iUserId, $iReturnMoney,$sName,$iPlayerId)
    {
        $oBetReturn = new self;
        $oBetReturn->betorderid = $iBetOrderId;
        $oBetReturn->userid = $iUserId;
        $oBetReturn->returnmoney = $iReturnMoney;
        $oBetReturn->name = $sName;
        $oBetReturn->playerid = $iPlayerId;
        $oBetReturn->save();
    }

    public static function aGetReturnRecordList($iUserId)
    {
    	return self::where('userid',$iUserId)->get();
    }
}