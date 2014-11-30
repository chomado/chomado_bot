<?php
require_once('twitteroauth/twitteroauth.php');
require_once('botconfig.php');

// ファイルの行をランダムに抽出
$filelist = file('list.txt');

// 同じ発言さっきしたよねってerror出まくるので時間を入れることにした. タイムゾーンはJST指定
$date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

if ( shuffle($filelist) )
{
	$message = $filelist[0] . ' ' . $date->format('Y/m/d H:i');
}

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$req = $connection -> OAuthRequest(
	"https://api.twitter.com/1.1/statuses/update.json"
	, "POST"
	, array("status"=> $message )
	);

var_dump($req);
var_dump($message);