<?php
require_once('twitteroauth/twitteroauth.php');
require_once('static_data/botconfig.php');
require_once('GetWeather.php');

// 現在時刻. タイムゾーンはJST指定
$time = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

// ファイルの行をランダムに抽出
$filelist 	= file('tweet_content_data_list/list.txt');
shuffle($filelist);
$message 	= $filelist[0] . PHP_EOL;

// 現在の天気と明日の予報を入手
$weather	= new GetWeather('tokyo');

$now 		= $weather->GetCondition();
$tomorrow 	= $weather->GetTomorrow();

// 呟く文に天気情報を加える
$message	.= $now['title']
			. '('
			. $time->format('m/d H:i')
			. '現在) は'
			. $now['weather']
			. '('
			. $now['temp']
			. '℃)です.'
			. PHP_EOL
			. '明日は'
			. $tomorrow["weather"]
			. 'で, '
			. '最高気温は'
			. $tomorrow['high']
			. '℃, 最低気温は'
			. $tomorrow['low']
			. '℃ です.';

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$req = $connection -> OAuthRequest(
	"https://api.twitter.com/1.1/statuses/update.json"
	, "POST"
	, array("status"=> $message )
	);

//var_dump($req);
var_dump($message);