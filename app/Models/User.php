<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Manage\Children;
use App\Models\Product\BetReturn;


class User extends Authenticatable
{
    private $iDeductAmount = false;
    private $iAddAmount = false;
    private $oParentUser = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','parentid','ancestorids','keeppoint','holdmoney','availablemoney','totalmoney'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

        public function aGetAllChildrenList()
    {
        return Children::aGetAllChildrenList($this->id);
    }

    public function aGetDirectUserChildrenList()
    {
        return Children::aGetDirectUserChildrenList($this->id);
    }

    public function oGetChildData($iChildId)
    {
        return Children::oGetChildData($iChildId);
    }

    public function bIsChild($iChildId)
    {
        return self::where('id',$iChildId)->whereRaw("FIND_IN_SET({$this->id},ancestorids)")->exists();
    }

    public static function bMailExist($sMail)
    {
        return self::where('email',$sMail)->exists();
    }

    public function bIsSelf($iUserId)
    {
        return ($iUserId==$this->id);
    }

    public function vCreateChild($aInput)
    {
        $aInput['parentid'] = $this->id;
        $aInput['ancestorids'] = ($this->ancestorids=='0')?$this->id:sprintf("%s,%s",$this->ancestorids,$this->id);
        Children::vCreateChild($aInput);
    }

    public function bIsTopUser()
    {
        return (intval($this->parentid)==0);
    }

    public function vSetDeductMoneyAmount($iMoney)
    {
        if($this->iDeductAmount==false)
        {
            $this->iDeductAmount = 0;
        }

        $this->iDeductAmount += $iMoney;
    }

    public function vSetAddMoneyAmount($iMoney)
    {
        if($this->iAddAmount==false)
        {
            $this->iAddAmount = 0;
        }

        $this->iAddAmount += $iMoney;
    }

    public function vSetMoneyAdd()
    {
        if($this->iAddAmount==false)
        {
            return;
        }
        $this->totalmoney += $this->iAddAmount;
        $this->availablemoney += $this->iAddAmount;
        $this->save();
        $this->iAddAmount = false;
    }

    public function vSetMoneyDeduct()
    {
        if($this->iDeductAmount==false)
        {
            return;
        }
        $this->totalmoney -= $this->iDeductAmount;
        $this->availablemoney -= $this->iDeductAmount;
        $this->save();
        $this->iDeductAmount = false;
    }

    public function vReturnPointToSelf($iBetOrderId,$iBetMoney)
    {
        $iReturnMoney = $this->iGetMoneyReturnToSelf($iBetMoney);
        BetReturn::vSetBetReturn($iBetOrderId, $this->id, $iReturnMoney);
        $this->vSetAddMoneyAmount($iReturnMoney);
        $this->vSetMoneyAdd();
    }

    public function vReturnPointToParent($iBetOrderId,$iBetMoney)
    {
        if($this->parentid==0)
        {
            return;
        }
        $oParentUser = $this->oGetParentUser();
        $iReturnMoney = $this->iGetMoneyReturnToParent($iBetMoney);
        BetReturn::vSetBetReturn($iBetOrderId, $this->parentid, $iReturnMoney);
        $oParentUser->vSetAddMoneyAmount($iReturnMoney);
        $oParentUser->vSetMoneyAdd();
        $oParentUser->vReturnPointToParent($iBetOrderId,$iBetMoney);
    }

    private function iGetMoneyReturnToSelf($iBetMoney)
    {
        return ($this->keeppoint/100) * $iBetMoney ;
    }

    private function iGetMoneyReturnToParent($iBetMoney)
    {
        return $this->iGetDiffPoint() * $iBetMoney ;
    }

    private function iGetDiffPoint()
    {
        if($this->oParentUser==false)
        {
            $this->oParentUser = $this->oGetParentUser();
        }
        return ($this->oParentUser->keeppoint - $this->keeppoint)/100;
    }

    public function oGetParentUser()
    {
        if($this->parentid==0)
        {
            return;
        }
        if($this->oParentUser==false)
        {
            $this->oParentUser = User::find($this->parentid);
        }
        return $this->oParentUser;
    }

    public function bIsAvailableMoneyEnough($iMoney)
    {
        return ($this->availablemoney>=$iMoney);
    }

    public function vFundFrozen($iMoney)
    {
        $this->holdmoney += $iMoney;
        $this->availablemoney -= $iMoney;
        $this->save();
    }

    public function vFundDefrozen($iMoney)
    {
        $this->holdmoney -= $iMoney;
        $this->availablemoney += $iMoney;
        $this->save();
    }
}
