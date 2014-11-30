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
// 現在の気温
$temp = GetWeather()
            ->query->results->channel->item->condition->temp;
// 明日の天気
$tomorrow_weather = GetWeather()
            ->query->results->channel->item->forecast[1]->text;
// 明日の最高気温
$tomorrow_high_temp = GetWeather()
            ->query->results->channel->item->forecast[1]->high;
// 明日の最低気温
$tomorrow_low_temp = GetWeather()
            ->query->results->channel->item->forecast[1]->low;
// 華氏→摂氏変換関数
function FtoC($f)
{
    return round(($f - 32) * 0.555, 1);
}

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
                . 'です.'
                . PHP_EOL
                . '明日は'
                . $tomorrow_weather
                . 'で, '
                . '最高気温は'
                . FtoC($tomorrow_high_temp)
                . '度, 最低気温は'
                . FtoC($tomorrow_low_temp)
                . '度です.';
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