<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Manage\Children;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Lottery\LotteryOrder;
use App\Models\Product\BetReturn;
use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\Threed\Jisupailie3;
use App\Models\Product\Card\Poke\BlackjackRecord;


class ChildrenController extends Controller
{
    private $oUser = null;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($iId)
    {
        $oUser = Auth::user();
        if(!$this->bPageAuthCheck($iId))
        {
            return $this->vRedirector(Auth::user()->id);
        }

        $aChildrenList = Children::aGetAllChildrenList($iId);
        return view(
                    'manage.children.index',
                    [
                        'aChildrenList' => $aChildrenList,
                        'iTargetUserId' => $iId
                    ]
                );
    }

    public function betRecord($iId)
    {
        if(!$this->bPageAuthCheck($iId))
        {
            return $this->vRedirector(Auth::user()->id);
        }
        $aLotteryOrderList = LotteryOrder::aGetBetRecordList($iId);
        return view('manage.children.betrecord',
                    [
                        'aLotteryOrderList' => $aLotteryOrderList
                    ]
        );
    }


    public function returnRecord($iId)
    {
        if(!$this->bPageAuthCheck($iId))
        {
            return $this->vRedirector(Auth::user()->id);
        }

        $aReturnRecordList = BetReturn::aGetReturnRecordList($iId);
        return view('manage.children.returnrecord',
                    [
                        'aReturnRecordList' => $aReturnRecordList
                    ]
        );
    }

    public function gameRecord($iId)
    {
        if(!$this->bPageAuthCheck($iId))
        {
            return $this->vRedirector(Auth::user()->id);
        }
        $aGameRecordList = BlackjackRecord::aGetRecordList($iId);
        return view('manage.children.gamerecord',
                    [
                        'aGameRecordList' => $aGameRecordList
                    ]
        );
    }

    public function gameRecordDetail($iId)
    {
        $oGameRecord = BlackjackRecord::oGetGameRecord($iId);
        $oGameRecord->aUserIds = json_decode($oGameRecord->userids,true);

        if(!$this->bPageAuthCheckByIdList($oGameRecord->aUserIds))
        {
            return $this->vRedirector(Auth::user()->id);
        }
        $oGameRecord->aDetail = json_decode($oGameRecord->detail,true);
        return view('manage.children.gamerecorddetail',
                    [
                        'oGameRecord' => $oGameRecord
                    ]
        );
    }

    public function betRecordDetail($iId)
    {
        $oGameRecord = BlackjackRecord::oGetGameRecord($iId);
        $oGameRecord->aUserIds = json_decode($oGameRecord->userids,true);

        if(!$this->bPageAuthCheckByIdList($oGameRecord->aUserIds))
        {
            return $this->vRedirector(Auth::user()->id);
        }
        $oGameRecord->aDetail = json_decode($oGameRecord->detail,true);
        return view('manage.children.gamerecorddetail',
                    [
                        'oGameRecord' => $oGameRecord
                    ]
        );
    }


    public function create(Request $oRequest)
    {
        $aInput = $oRequest->all();
        if(!empty($aInput))
        {
            $aCheckData = $this->aPostCreateCheck($aInput);
            if($aCheckData['status']==false)
            {
                return ['status'=>false,'message'=> $aCheckData['message']];
            }
            else
            {
                Auth::user()->vCreateChild($aInput);
                return ['status'=>true];
            }
        }
        return view('manage.children.create');
    }

    private function aPostCreateCheck($aInput)
    {
        if($this->bIsValidEmail($aInput['email'])!=true )
        {
            return ['status'=> false, 'message'=>'電郵格式錯誤'];
        }

        if(User::bMailExist($aInput['email'])==true)
        {
            return ['status'=> false, 'message'=>'電郵已存在'];
        }

        if($aInput['password']!=$aInput['passwordconfirm'] || !is_string($aInput['password']) )
        {
            return ['status'=> false, 'message'=>'密碼確認錯誤'];
        }

        if(!is_string($aInput['name']) || strlen($aInput['name'])>50 || strlen($aInput['name'])<0)
        {
            return ['status'=> false, 'message'=>'名稱錯誤'];
        }

        if($aInput['keeppoint'] > Auth::user()->keeppoint)
        {
            return ['status'=> false, 'message'=>'保留返點錯誤'];
        }

        return ['status'=> true];
    }

    private function bIsValidEmail($sEmail)
    {
        return filter_var($sEmail, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function delete()
    {
        return view('manage.children.delete');
    }

    public function show()
    {
        return view('manage.children.show');

    }

    private function bPageAuthCheckByIdList($aUserIdList)
    {
        $bAuth = false;
        foreach ($aUserIdList as $iUserId)
        {
            $bAuth = $this->bPageAuthCheck($iUserId);
            if($bAuth==true)
            {
                break;
            }
        }
        return $bAuth;
    }

    private function bPageAuthCheck($iId)
    {
        $oUser = Auth::user();
        return ($oUser->bIsSelf($iId) || $oUser->bIsChild($iId));
    }



    private function vRedirector($iId)
    {
        return redirect()->action("Manage\ChildrenController@index",[$iId]);
    }
}
