<?php
date_default_timezone_set("Asia/Taipei");
error_reporting(E_ALL);
$sUrl = "https://www.caipiaokong.com/lottery/shssl.html";

$sStartTime = "10:34";
$sEndTime = "21:34";

$sServerName = "127.0.0.1";
$sUserName = "root";
$sPassword = "root";
$sDBName = "boyigame";

$sDateTime = date("Y-m-d H:i");
$sCreatedTime = date("Y-m-d H:i:s");
$sLottery = "shanghaishrshrle";
vTimeCheck($sStartTime, $sEndTime);



$sIssue = sGetIssue($sStartTime);
$sHtml = sGetHtml($sUrl);
// echo $sHtml;
$sCodeList = sGetCodeList($sHtml);
echo "<pre>"; print_r($sCodeList);

$oConnection = new mysqli($sServerName, $sUserName, $sPassword, $sDBName);
if ($oConnection->connect_error)
{
    die("连接失败: " . $oConnection->connect_error);
}

$sSql = sGetSql($sDateTime,$sIssue,$sCodeList,$sLottery,$sCreatedTime,$sCreatedTime);
$oConnection->query($sSql);

$oConnection->close();


function sGetIssue($sStartTime)
{
	sleep(1);
	$iStartTime = strtotime(date("Y-m-d ").$sStartTime);
	$iCurrentTime = time();
	$iTimeDiff = $iCurrentTime - $iStartTime;
	$iIssue = ceil($iTimeDiff / 1800);
	$sIssue = ($iIssue<10)?"0".$iIssue:$iIssue;
	$sIssue = date("Ymd")."-".$iIssue;
	return $sIssue;
}

function sGetHtml($sUrl)
{
	return null;
	$oCurl = curl_init();
	curl_setopt($oCurl, CURLOPT_URL, $sUrl);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
	$sAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.104 Safari/537.36 Core/1.53.2141.400 QQBrowser/9.5.10219.400';
	curl_setopt($oCurl, CURLOPT_USERAGENT, $sAgent);
	$sHtml = curl_exec($oCurl);
	curl_close($oCurl);
	return $sHtml;
}

function vTimeCheck($sStartTime, $sEndTime)
{
	if(date("H:i") < $sStartTime || $sEndTime < date("H:i"))
	{
		echo date("H:i")."EXIT";
		exit;
	}
}

function sGetCodeList($sHtml)
{
	if($sHtml==null)
	{
		$sIssue = sGetIssue("10:34");
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

function sGetSql($sDateTime,$sIssue,$sCodeList,$sLottery,$sCreatedTime)
{
	return "INSERT INTO issueinfo (`datetime`,`issue`,`code`,`lottery`,`updated_at`,`created_at`) VALUES ('".$sDateTime."','".$sIssue."','".$sCodeList."','".$sLottery."','".$sCreatedTime."','".$sCreatedTime."') ;";
}





//https://www.caipiaokong.com/lottery/shssl.html