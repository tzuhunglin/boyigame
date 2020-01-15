<?php
namespace App\Libraries;

use URL;
class MainLib
{
    public static function sGetIndexLink(){ return URL::route('blackjack.index');}
}
