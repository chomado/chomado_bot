<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー

// 最終投稿IDを取得
$param['since_id'] = file_get_contents('tweet_content_data_list/last_id.txt');
if (empty($param['since_id'])) {
	$param = null;
}

// ファイルの行をランダムに抽出
$reply_list = file('tweet_content_data_list/reply_list.txt');
shuffle($reply_list);
$index 		= 0;

// Twitterに接続
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

// リプライを取得
$res = $connection->get('statuses/mentions_timeline', $param);

if (!empty($res))
{
	foreach ($res as $re) 
	{
		// もし自分自身宛てだったら無視する.(無限ループになっちゃうから)
		if ($re->user->screen_name === 'chomado_bot')
			continue;
		// ツイート本文
		$message = $reply_list[$index];

		$param['status'] = sprintf('@%s %sさん%s%s'
			, $re->user->screen_name
			, trim(preg_replace('!([@＠#＃.]|://)!u', " $1 ", $re->user->name))
			, PHP_EOL
			, $message
			);
		$index = $index < count($reply_list) - 1 ? $index + 1 : 0;

		$param['in_reply_to_status_id'] = $re->id_str;
		// 投稿
		$connection->post('statuses/update', $param);
	}
	// 最終投稿IDを書き込む
	file_put_contents('last_id.txt', $res[0]->id_str);
}
