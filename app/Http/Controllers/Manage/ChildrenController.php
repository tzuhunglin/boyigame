<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Manage\Children;
use Illuminate\Support\Facades\Auth;

class ChildrenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $oUser = null;
    public function __construct()
    {
        $this->middleware('auth');

        // $this->oUser = Auth::user();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    public function betRecord($iId)
    {
        echo "<pre>"; print_r($iId);exit;
        return view('manage.children.berrecord');
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
