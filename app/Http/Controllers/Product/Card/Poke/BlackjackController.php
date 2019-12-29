<?php

namespace App\Http\Controllers\Product\Card\Poke;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Lottery\Threed\Jisupailie3;
use Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Product\Lottery\IssueInfoPushData;
use App\Models\Product\Lottery\LotteryBet;

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
        echo "fdafs";exit;
    }

}
