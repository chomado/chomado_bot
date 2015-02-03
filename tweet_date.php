<?php
namespace bot;
use Abraham\TwitterOAuth\TwitterOAuth;

// bootstrap
require_once(__DIR__ . '/vendor/autoload.php');
Log::setErrorHandler();

// ファイルの行をランダムに抽出
Log::trace("list.txtを読み込みます。");
$filelist = file(__DIR__ . '/tweet_content_data_list/list.txt');
shuffle($filelist);
Log::trace("list.txtは" . count($filelist) . "行です");

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

$param = [
    'status' => $message,
];

Log::info("Twitter に tweet を POST します:");
Log::info($param);

// 投稿
for($retry = 0; $retry < 3; ++$retry) {
    if($retry > 0) {
        sleep(1);
    }
    $result = $connection->post('statuses/update', $param);
    if(is_object($result) &&
       isset($result->id_str) &&
       isset($result->text))
    {
        Log::success("Tweet を投稿しました");
        Log::success(array('id' => $result->id_str, 'text' => $result->text));
        exit(0);
    }
    Log::warning("Tweet の投稿に失敗しました");
}
Log::error("Tweet を投稿できませんでした");
Log::error($param);
exit(1);
