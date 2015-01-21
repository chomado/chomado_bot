<?php
require_once('twitteroauth/twitteroauth.php'); // OAuth
require_once('static_data/botconfig.php'); // Twitterの各アクセスキー

// 最終投稿IDを取得
$param['since_id'] = file_get_contents('last_id.txt');
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
		$param['status'] = sprintf('@%s %sさん%s%s'
			, $re->user->screen_name
			, str_replace('@', '_', $re->user->name)
			, PHP_EOL
			, $reply_list[$index]
			);
		$index = $index < count($reply_list) - 1 ? $index + 1 : 0;

		$param['in_reply_to_status_id'] = $re->id_str;
		// 投稿
		$connection->post('statuses/update', $param);
	}
	// 最終投稿IDを書き込む
	file_put_contents('last_id.txt', $res[0]->id_str);
}