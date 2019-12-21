<?php
namespace App\Libraries\Product\Lottery\Threed;

use URL;
class Jisupailie3Lib
{
	public static $sCodeSourceUrl = "https://www.1395p.com/shssl/kaijiang";
    public static function sGetIndexLink(){ return URL::route('jisupailie3.index');}
    public static function sGetBetLink(){ return URL::route('jisupailie3.bet');}

}
