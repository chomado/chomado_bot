<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;
use Abraham\TwitterOAuth\TwitterOAuth;
use bot\DateTime;
use bot\RandomSentenceList;
use bot\format\DateTimeFormatter;

// bootstrap
require_once(__DIR__ . '/vendor/autoload.php');
Log::setErrorHandler();

// ファイルの行をランダムに抽出
$randomComments = new RandomSentenceList(__DIR__ . '/tweet_content_data_list/list.txt');
Log::trace("list.txtは" . count($randomComments) . "行です");

// 現在日時
$now = new DateTime('now', new \DateTimeZone('Asia/Tokyo'));

// 呟く文成形
$message = sprintf(
    "%s\n\n%s",
    $randomComments->get(),
    DateTimeFormatter::formatDateTime($now) // 『今日2015/01/20は第04週目の火曜です。今年の5.2%が経過しました。』
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
$ret = TwitterUtil::postTweet($connection, $param);
exit($ret ? 0 : 1);
