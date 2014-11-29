<?php
require_once('twitteroauth/twitteroauth.php');
require_once('botconfig.php');

// ファイルの行をランダムに抽出
$filelist = file('list.txt');

if ( shuffle($filelist) )
{
	$message = $filelist[0];
}

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$req = $connection -> OAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=> $message ));

var_dump($req);