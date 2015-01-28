<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー
require_once('class/weather.php'); // Weatherクラスが入ってる

// ファイルの行をランダムに抽出
$filelist   	= file(dirname(__FILE__) 
		. '/tweet_content_data_list/list.txt');
shuffle($filelist);

// 呟く文成形 
$message 	= $filelist[0] // (*ﾟ▽ﾟ* っ)З ちょまぎょ!
		. PHP_EOL . PHP_EOL
		//『東京の現在(00:03)の天気はうす曇り(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です』
		. (new Weather('tokyo'))
			->GetWeatherMessage((new DateTime('now', new DateTimeZone('Asia/Tokyo')))); 

// Twitterに接続
$connection 	= new TwitterOAuth(
			  CONSUMER_KEY
			, CONSUMER_SECRET
			, ACCESS_TOKEN
			, ACCESS_TOKEN_SECRET
		);
// 投稿
$connection->post('statuses/update', array("status"=> $message ));

var_dump($message);