<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー
require_once('weather.php'); // Weatherクラスが入ってる
require_once('date.php'); // Dateクラスが入ってる

// ファイルの行をランダムに抽出
$filelist   = file('tweet_content_data_list/list.txt');
shuffle($filelist);
$message    = $filelist[0] . PHP_EOL;

// 現在時刻. タイムゾーンはJST指定
$time = new Date();
// 経過日数%表示『今日で今年の約5.0%が過ぎました』
$message    .= $time->GetDateMessage();

// 現在の天気と明日の予報を入手
$weather    = new Weather('tokyo');

$now        = $weather->GetCondition();
$tomorrow   = $weather->GetTomorrow();

// 呟く文に天気情報を加える
$message    .= '東京の現在('
            . $time->GetTime()->format('H:i')
            . ')の天気は'
            . $now['weather']
            . '('
            . $now['temp']
            . '℃)です。'
            . PHP_EOL
            . '明日は'
            . $tomorrow['weather']
            . 'で、'
            . '最高'
            . $tomorrow['high']
            . '℃、最低'
            . $tomorrow['low']
            . '℃です'
            . PHP_EOL;

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$req = $connection -> OAuthRequest(
    "https://api.twitter.com/1.1/statuses/update.json"
    , "POST"
    , array("status"=> $message )
    );

//var_dump($req);
var_dump($message);