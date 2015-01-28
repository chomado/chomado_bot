<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー
require_once('class/date.php'); // Dateクラスが入ってる

// ファイルの行をランダムに抽出
$filelist 	= file(dirname(__FILE__) 
		. '/tweet_content_data_list/list.txt');
shuffle($filelist);

// 呟く文成形
$message 	= $filelist[0] . PHP_EOL . PHP_EOL
		. (new Date())->GetDateMessage();//『今日2015/01/20は第04週目の火曜です。今年の5.2%が経過しました。』

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