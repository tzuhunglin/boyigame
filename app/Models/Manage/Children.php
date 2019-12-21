<?php

namespace App\Models\Manage;

use Illuminate\Foundation\Auth\User ;
use DB;

class Children extends User
{
    protected $table = 'users';
    public static function aGetAllChildrenList($iUserId)
    {
        if(empty($iUserId)|| !is_numeric($iUserId))
        {
            return null;
        }
        $aAllChildrenList = self::whereRaw("FIND_IN_SET({$iUserId},ancestorids)")->get();
        return $aAllChildrenList;
    }

    public static function aGetDirectUserChildrenList($iUserId)
    {
        if(empty($iUserId)|| !is_numeric($iUserId))
        {
            return null;
        }
        $aDirectUserChildrenList = self::where('parentid',$iUserId)->get();
        return $aDirectUserChildrenList;
    }

    public static function vCreateChild($aChildData)
    {
        $oUser = new Children();
        $oUser->name = $aChildData['name'];
        $oUser->email = $aChildData['email'];
        $oUser->password = bcrypt($aChildData['password']);
        $oUser->parentid = $aChildData['parentid'];
        $oUser->ancestorids = $aChildData['ancestorids'];
        $oUser->keeppoint = $aChildData['keeppoint'];
        $oUser->holdmoney = 0;
        $oUser->availablemoney = 1000;
        $oUser->totalmoney = 1000;

        $oUser->save();
    }

    public static function oGetChildData($iChildId)
    {
        if(empty($iChildId)|| !is_numeric($iChildId))
        {
            return null;
        }

        return self::find($iChildId);
    }
}