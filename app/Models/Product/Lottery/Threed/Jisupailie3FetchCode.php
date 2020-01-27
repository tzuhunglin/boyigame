<?php
namespace App\Models\Product\Lottery\Threed;

use App\Models\Product\Lottery\IssueInfo;
use App\Models\Product\Lottery\Threed\Jisupailie3;

class Jisupailie3FetchCode
{
    private $sStartTime = "10:34";
    private $sEndTime = "21:34";
    private $sUrl = "https://www.caipiaokong.com/lottery/shssl.html";

    function __construct()
    {
    }

    public function vExecute()
    {
        if($this->bIsInValidTime()==false)
        {
            return;
        }
        $this->sIssue = Jisupailie3::sGetLastDrawnIssue();
        $sHtml = $this->sGetHtml($this->sUrl);
        $this->sCodeList = $this->sGetCodeList($sHtml);
        $this->vSetIssueInfo();
    }

    private function vSetIssueInfo()
    {
        $oIssueInfo = new IssueInfo();
        $oIssueInfo->datetime = date("Y-m-d H:i");
        $oIssueInfo->issue = $this->sIssue;
        $oIssueInfo->code = $this->sCodeList;
        $oIssueInfo->lottery = Jisupailie3::$sLottery;
        $oIssueInfo->save();
    }

    private function bIsInValidTime()
    {
        return (date("H:i") > $this->sStartTime && date("H:i") < $this->sEndTime);
    }

    private function sGetHtml($sUrl)
    {
        if(date("Y-m-d") < "2020-02-01")
        {
            return null;
        }
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $sUrl);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        $sAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.104 Safari/537.36 Core/1.53.2141.400 QQBrowser/9.5.10219.400';
        curl_setopt($oCurl, CURLOPT_USERAGENT, $sAgent);
        $sHtml = curl_exec($oCurl);
        curl_close($oCurl);
        return $sHtml;
    }

    private function sGetCodeList($sHtml)
    {
        if($sHtml==null)
        {
            $sIssue = $this->sIssue;
            $sIssue = "0".explode("-",$sIssue)[1];
            $aCodeList = str_split($sIssue, 1);
            return json_encode($aCodeList);
        }
        preg_match_all("/全站禁止非法采集，如需数据请购买接口(.*)组/",$sHtml,$aMatches);
        preg_match_all('/<strong.*?>(.*?)(?=<\/strong>)/im', $aMatches[0][0], $aMatches2);
        $aCodeList = array(
                        (String)$aMatches2[1][0],
                        (String)$aMatches2[1][1],
                        (String)$aMatches2[1][2]
                    );
        $sCodeList = json_encode($aCodeList);
        echo "<pre>"; print_r($sCodeList);
        return $sCodeList;
    }
}
