<?php
namespace App\Libraries\Manage;

use URL;

class ChildrenLib
{
    public static function sGetIndexLink($iId){ return URL::route('children.index',[$iId]);}
    public static function sGetCreateLink(){ return URL::route('children.create');}
    public static function sGetEditLink($iId){ return URL::route('children.edit',[$iId]);}
    public static function sGetDeleteLink($iId){ return URL::route('children.delete',[$iId]);}
    public static function sGetBetRecordLink($iId){ return URL::route('children.betRecord',[$iId]);}
    public static function sGetReturnRecordLink($iId){ return URL::route('children.returnRecord',[$iId]);}
    public static function sGetGameRecordLink($iId){ return URL::route('children.gameRecord',[$iId]);}
    public static function sGetBetRecordDetailLink($iId){ return URL::route('children.betRecordDetail',[$iId]);}
    public static function sGetGameRecordDetailLink($iId){ return URL::route('children.gameRecordDetail',[$iId]);}



}
