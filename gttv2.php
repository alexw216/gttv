<?php
//created by alexw216
//for gttv iptv url proxy rendering
// 2018/11/2 V1

error_reporting(E_ALL);

date_default_timezone_set('America/Los_Angeles');
$now   = time();

$contentId = $_GET['contentId'];
$streamingId = $_GET['streamingId'];


//$contentId = '13797';
//$streamingId = '13645';


$filename = dirname(__FILE__).'/gttv';
//echo ($now - filemtime($filename));
//echo "\n";
//echo (60 * 60 * 24 * 0.125);
if (file_exists($filename)  && ($now - filemtime($filename) <= 60 * 60 * 24 * 0.02) )
{
//echo "$filename was last modified: " . date ("F d Y H:i:s.", filemtime($filename));
$gttv = fopen('gttv','r+');
//$token = fgets($yhtv);
$token = fgets($gttv);
fclose($gttv);



$HArray = array('Content-Type: application/x-www-form-urlencoded;','Authorization: Bearer '.$token,'Content-Type: application/json; charset=utf-8','User-Agent: Dalvik/2.1.0','Connection: Keep-Alive','Accept-Encoding: gzip');
$Live = 'http://gtapi.wowotv.tw/gttv-api_v2/streaming/get';
$LPost = 'contentId='.$contentId.'&streamingId='.$streamingId.'&';
$PData = Post_Url($Live,$LPost,$HArray);
$JxUrl = json_decode($PData);

$streamingUrl = $JxUrl->data->{'streamingUrl'};
//var_dump($JxUrl);

if(strpos($streamingUrl,'http')!== false)
{
	$streamingUrl = str_replace('gtlive.wowotv.tw','210.201.54.100',$streamingUrl);
	$s1 = strpos($streamingUrl,'http://');
	$s2 = strpos($streamingUrl,'playlist');
	$JxServer = substr($streamingUrl,$s1,$s2-$s1);
	$JxPlayID = Get_PlayID($streamingUrl,$JxServer);
	$PlayUrl = Get_PlayUrl($JxPlayID,$JxServer);
	echo $PlayUrl;
}


}

else 
{
	$Login = 'http://gtapi.wowotv.tw/gttv-api_v2/token/get';
	$LPost = '';
	$HArray = array('IMEI: xxxx','Model: xxxx','IMSI: xxxx','UUID: xxxx','Content-Type: application/x-www-form-urlencoded;','DevToken: xxxx','Platform: Android Tablet','Version: 5009','Authorization: Basic xxxx','User-Agent: Dalvik/2.1.0','Connection: Keep-Alive','Accept-Encoding: gzip');
	$LData = Post_Token($Login,"",$HArray);
	$JxUrl = json_decode($LData);
	$token = $JxUrl->data->{'accessToken'};
	$mytoken = fopen('gttv','w+');
	fwrite($mytoken, $token);
    fclose($mytoken);
	$HArray = array('Content-Type: application/x-www-form-urlencoded;','Authorization: Bearer '.$token,'Content-Type: application/json; charset=utf-8','User-Agent: Dalvik/2.1.0','Connection: Keep-Alive','Accept-Encoding: gzip');
	$Live = 'http://gtapi.wowotv.tw/gttv-api_v2/streaming/get';
	$LPost = 'contentId='.$contentId.'&streamingId='.$streamingId.'&';
	$PData = Post_Url($Live,$LPost,$HArray);
	$JxUrl = json_decode($PData);
	$streamingUrl = $JxUrl->data->{'streamingUrl'};
	$streamingUrl = str_replace('gtlive.wowotv.tw','210.201.54.100',$streamingUrl);
	$s1 = strpos($streamingUrl,'http://');
	$s2 = strpos($streamingUrl,'playlist');
	$JxServer = substr($streamingUrl,$s1,$s2-$s1);
	$JxPlayID = Get_PlayID($streamingUrl,$JxServer);
	$PlayUrl = Get_PlayUrl($JxPlayID,$JxServer);
	echo $PlayUrl;
}




function Get_PlayUrl($PUrl,$PSer)
{
		$HArray = array('User-Agent: VasCreativePlayer/02.12.0153 (Linux;Android 5.1.1) ExoPlayerLib/2.0.0','Connection: Keep-Alive','Accept-Encoding: gzip');
		$FwUrl = Post_Url($PUrl,"",$HArray);
		$OutPut = str_replace("media",$PSer."media",$FwUrl);
		return $OutPut;
}	



function Get_PlayID($JxUrl,$JxServer)
{
	$HArray = array('User-Agent: VasCreativePlayer/02.12.0153 (Linux;Android 5.1.1) ExoPlayerLib/2.0.0;','Connection: Keep-Alive','Accept-Encoding: gzip');
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $JxUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $HArray);
	$JxData = curl_exec($curl);
	$s1 = strpos($JxData,'chunklist');
    $s2 = strpos($JxData,'%3D%3D');
    $FData = substr($JxData,$s1,$s2-$s1+6);
	curl_close($curl);
	$output = $JxServer.$FData;
	return $output;
}


function Post_Url($Url,$Post,$Header){  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $Url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $Post);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$Header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
}


function Post_Token($Url,$Post,$Header){  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $Url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $Post);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$Header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
}


?>
