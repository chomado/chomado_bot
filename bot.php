<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー
require_once('weather.php'); // Weatherクラスが入ってる
require_once('date.php'); // Dateクラスが入ってる

// ファイルの行をランダムに抽出
$filelist   = file('tweet_content_data_list/list.txt');
shuffle($filelist);
$message    = $filelist[0] . PHP_EOL;

// Dateクラスと天気クラス
$time       = new Date();
$weather    = new Weather('tokyo');

// 呟く文成形
$message    .= $time->GetDateMessage()//『今日2015/01/20は第04週目の火曜です。今年の5.2%が経過しました。』
            . PHP_EOL
            . $weather->GetWeatherMessage($time); //『東京の現在(00:03)の天気はうす曇り(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です』

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$req = $connection->OAuthRequest(
        "https://api.twitter.com/1.1/statuses/update.json"
        , "POST"
        , array("status"=> $message )
    );

var_dump($message);