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
        $iUserId = Auth::user()->id;
        $oBlajack = new Blackjack($iUserId);
        $oGameData = $oBlajack->oUnfinishedGameData;
        // Event(new PushGameDataBlackjack($oGameData));
        return view('product.card.poke.blackjack.index',[
            // 'sGameData' => json_encode($oGameData),
            'sHashKey' => $oGameData->sHashKey,

            'iUserId' => $iUserId
        ]);
    }

}
