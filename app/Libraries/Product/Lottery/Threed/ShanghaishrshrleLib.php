<?php
namespace App\Libraries\Product\Lottery\Threed;

use URL;
class ShanghaishrshrleLib
{
	public static $sCodeSourceUrl = "https://www.1395p.com/shssl/kaijiang";
    public static function sGetIndexLink(){ return URL::route('shanghaishrshrle.index');}
    public static function sGetBetLink(){ return URL::route('shanghaishrshrle.bet');}
}
