<?php
namespace App\Libraries\Product\Card\Poke;

use URL;
class BlackjackLib
{
    public static function sGetIndexLink(){ return URL::route('blackjack.index');}
    public static function sGetSumUpLink(){ return URL::route('blackjack.sumup');}

}
