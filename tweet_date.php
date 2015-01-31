<?php
require_once(__DIR__ . '/twitteroauth/twitteroauth.php'); // OAuth
require_once(__DIR__ . '/class/date.php'); // class Date
require_once(__DIR__ . '/class/config.php'); // class Config

// ファイルの行をランダムに抽出
$filelist 	= file(__DIR__ . '/tweet_content_data_list/list.txt');
shuffle($filelist);

// 呟く文成形
$message = sprintf(
    "%s\n\n%s",
    $filelist[0],
    (new Date())->GetDateMessage() //『今日2015/01/20は第04週目の火曜です。今年の5.2%が経過しました。』
);

// Twitterに接続
$config = Config::getInstance();
$connection = new TwitterOAuth(
    $config->getTwitterConsumerKey(),
    $config->getTwitterConsumerSecret(),
    $config->getTwitterAccessToken(),
    $config->getTwitterAccessTokenSecret()
);

// 投稿
$connection->post('statuses/update', array("status"=> $message ));

var_dump($message);
