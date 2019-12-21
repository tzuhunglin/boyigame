<?php
namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;


class BetReturn extends Model
{
    protected $table = 'betreturn';

    public static function vSetBetReturn($iBetOrderId, $iUserId, $iReturnMoney)
    {
        $oBetReturn = new self;
        $oBetReturn->betorderid = $iBetOrderId;
        $oBetReturn->userid = $iUserId;
        $oBetReturn->returnmoney = $iReturnMoney;
        $oBetReturn->save();
    }
}