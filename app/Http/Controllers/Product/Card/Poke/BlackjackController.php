<?php

namespace App\Http\Controllers\Product\Card\Poke;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Card\Poke\Blackjack;
use App\Events\Event;
use App\Events\PushGameDataBlackjack;
use App\Models\Product\Card\Poke\BlackjackRecord;
use App\Models\Product\Card\Poke\BlackjackAward;


class BlackjackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => [
            'sumup'
        ]]);
    }

    public function index()
    {
        $oUser = Auth::user();
        $iUserId = $oUser->id;
        $sHashKey = "";
        if($oUser->bIsTopUser())
        {
            $aData = array(
                'sHashKey' => $sHashKey,
                'iUserId' => $oUser->id,
                'bStatus'=> false,
                'sMessage'=> '總代不能投注，請使用下級帳號．',
                'sAppUrl' => env('APP_URL')
            );
        }
        else if(Blackjack::bIsPlayingInGame($oUser->id)==true)
        {
            $oBlajack = new Blackjack($oUser->id);
            $oGameData = $oBlajack->oUnfinishedGameData;
            $sHashKey = $oGameData->sHashKey;
            $aData = array(
                'sHashKey' => $sHashKey,
                'iUserId' => $oUser->id,
                'bStatus'=> true,
                'sMessage'=> '',
                'sAppUrl' => env('APP_URL')
            );
        }
        else
        {
            if($oUser->bIsAvailableMoneyEnough(Blackjack::$iGameMoneyLimit))
            {
                $oBlajack = new Blackjack($oUser->id);
                $oGameData = $oBlajack->oUnfinishedGameData;
                $sHashKey = $oGameData->sHashKey;
                $oUser->vFundFrozen(Blackjack::$iGameMoneyLimit);
                $aData = array(
                    'sHashKey' => $sHashKey,
                    'iUserId' => $oUser->id,
                    'bStatus'=> true,
                    'sMessage'=> '',
                    'sAppUrl' => env('APP_URL')
                );
            }
            else
            {
                $aData = array(
                    'sHashKey' => $sHashKey,
                    'iUserId' => $oUser->id,
                    'bStatus'=> false,
                    'sMessage'=> '可用餘額不足',
                    'sAppUrl' => env('APP_URL')
                );
            }
        }
        return view('product.card.poke.blackjack.index',$aData);
    }

    public function sumup($sHashKey)
    {
        $oGameData = Blackjack::oGetGameData($sHashKey);

        if(empty($oGameData))
        {
            return ['status'=> false];
        }
        $oBlackjackAward = new BlackjackAward($sHashKey);
        $oGameData = $oBlackjackAward->oGetAwardedGameData();
        return json_encode($oGameData);
    }

}
