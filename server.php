<?php
require_once "jssdk.php";
require_once "config.php";

$server_id = $_POST['serverId'];

$jssdk = new JSSDK($wechat_config['appId'], $wechat_config['appSecret']);
$access_token = $jssdk->getAccessToken();

$media_id = $server_id;

$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

saveMedia($url);

function saveMedia($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $package = curl_exec($ch);
    $httpinfo = curl_getinfo($ch);

    curl_close($ch);
    $media = array_merge(array('mediaBody' => $package), $httpinfo);

    preg_match('/\w\/(\w+)/i', $media["content_type"], $extmatches);
    $fileExt = $extmatches[1];
    $filename = time().rand(100,999).".{$fileExt}";
    // make sure the document chmod 777
    file_put_contents($filename,$media['mediaBody']);
    return $filename;
}

echo 'save media success';