<?php
namespace App\Libraries\Product\Card\Poke;

use URL;
use App\Models\User;
class BlackjackLib
{
    public static function sGetIndexLink(){ return URL::route('blackjack.index');}
    public static function sGetSumUpLink($sHashKey){ return URL::route('blackjack.sumup',[$sHashKey]);}

	public static function sGetCard($iCode)
	{
	  $sSuit = self::sGetSuit($iCode);
	  $sItem = self::sGetCardItem($iCode);
	  return $sSuit.$sItem;
	}

	public static function sGetCardItem($iCode)
	{
	  $iItem = $iCode%13;
	  if($iItem == 0)
	  {
	    return "K";
	  }
	  else if($iItem == 1)
	  {
	    return "A";
	  }
	  else if($iItem == 11)
	  {
	    return "J";
	  }
	  else if($iItem == 12)
	  {
	    return "Q";
	  }
	  else
	  {
	    return $iItem;
	  }

	}

	public static function sGetSuit($iCode)
	{
	  $aSuits = ["♥","♠","♦","♣"];
	  if($iCode>0 && $iCode<14)
	  {
	    return $aSuits[0];
	  }

	  if($iCode>13 && $iCode<27)
	  {
	    return $aSuits[1];
	  }

	  if($iCode>26 && $iCode<40)
	  {
	    return $aSuits[2];
	  }

	    if($iCode>39)
	  {
	    return $aSuits[3];
	  }
	}

	public static function iGetUserBetSumUp($iUserId,$iBetAmount,$iDouble,$iWinLose)
	{
		if($iWinLose==0)
		{
			return ($iDouble!=3)?$iBetAmount * -1 : $iBetAmount * 2 * -1;
		}
		else
		{
			$oUser = User::find($iUserId);
			$iBetSumUp = $iBetAmount * (90 + $oUser->keeppoint )/ 100;
			return ($iDouble!=3)?$iBetSumUp : $iBetSumUp * 2;
		}
	}

	public static function iGetUserInsuranceSumUp($iUserId,$iBetAmount,$iInsurance,$aBankerCards,$aBankerPoints)
	{
		if($iInsurance!=3)
		{
			return 0;
		}

		if(count($aBankerCards)!=2 || !in_array(21, $aBankerPoints))
		{
			return $iBetAmount / 2 * -1;
		}
		else
		{
			return ($iBetAmount/2) * (90 + $oUser->keeppoint )/ 100;
		}
	}

	public static function iGetUserTotalSumUp($aUserInfoData,$aBankerCards,$aBankerPoints)
	{
		$iUserBetSumUp = self::iGetUserBetSumUp($aUserInfoData['iUserId'],$aUserInfoData['iBetAmount'],$aUserInfoData['iDouble'],$aUserInfoData['iWinLose']);
		$iUserInsuranceSumUp = self::iGetUserInsuranceSumUp($aUserInfoData['iUserId'],$aUserInfoData['iBetAmount'],$aUserInfoData['iInsurance'],$aBankerCards,$aBankerPoints);
		$iUserTotalSumUp = $iUserBetSumUp + $iUserInsuranceSumUp;
		return  $iUserTotalSumUp ;
	}
}
