<?php
require_once('twitteroauth/twitteroauth.php');
require_once('botconfig.php');

// 現在時刻. タイムゾーンはJST指定
$time = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

// yahoo の天気予報 API から引っ張ってくる
function GetWeather($city='tokyo')
{
    return json_decode(file_get_contents(
        'https://query.yahooapis.com/v1/public/yql?q=select%09*%20from%20%09weather.forecast%20%20where%20%09woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22' 
        . $city 
        . '%2C%20jp%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'
        ));
}
// 現在の天気
$weather = GetWeather()// いずれは都市をユーザが指定できるようにしたい
            ->query->results->channel->item->condition->text; 
// 明日の天気
$tomorrow_weather = GetWeather()
            ->query->results->channel->item->forecast[1]->text;

// ファイルの行をランダムに抽出
$filelist = file('list.txt');
if ( shuffle($filelist) )
{
    $message = $filelist[0]  
                . PHP_EOL 
                . '東京の現在('
                . $time->format('m/d H:i')
                . ')の天気は'
                . $weather
                . 'で, 明日の天気は'
                . $tomorrow_weather
                . 'です';
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