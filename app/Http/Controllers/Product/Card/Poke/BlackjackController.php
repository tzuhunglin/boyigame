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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $oUser = Auth::user();
        $iUserId = $oUser->id;
        $sHashKey = "";
        if(Blackjack::bIsPlayingInGame($oUser->id)==true)
        {
            $oBlajack = new Blackjack($oUser->id);
            $oGameData = $oBlajack->oUnfinishedGameData;
            $sHashKey = $oGameData->sHashKey;
        }
        else
        {
            if($oUser->bIsAvailableMoneyEnough(Blackjack::$iGameMoneyLimit))
            {
                $oBlajack = new Blackjack($oUser->id);
                $oGameData = $oBlajack->oUnfinishedGameData;
                $sHashKey = $oGameData->sHashKey;
                $oUser->vFundFrozen(Blackjack::$iGameMoneyLimit);
            }
        }
        return view('product.card.poke.blackjack.index',[
            'sHashKey' => $sHashKey,
            'iAvailableMoney' => $oUser->availablemoney,
            'iUserId' => $oUser->id,
            'iGameMoneyLimit' => Blackjack::$iGameMoneyLimit
        ]);
    }

    public function sumup($sHashKey)
    {
        $oGameData = Blackjack::oGetGameData($sHashKey);
        if(empty($oGameData))
        {
            return ['status'=> false];
        }
        BlackjackRecord::vUpdateByGameData($oGameData);
        $oBlackjackAward = new BlackjackAward($sHashKey);
        echo "<pre>"; print_r($oBlackjackAward);exit;

    }

}
