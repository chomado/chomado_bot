<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;
use Abraham\TwitterOAuth\TwitterOAuth;
use bot\RandomSentenceList;

// bootstrap
require_once(__DIR__ . '/vendor/autoload.php');
Log::setErrorHandler();

// ファイルの行をランダムに抽出
$randomComments = new RandomSentenceList(__DIR__ . '/tweet_content_data_list/list.txt');
Log::trace("list.txtは" . count($randomComments) . "行です");

// 呟く文成形 
$message = sprintf(
    "%s\n\n%s",
    $randomComments->get(), // (*ﾟ▽ﾟ* っ)З ちょまぎょ!
    //『東京の現在(00:03)の天気はうす曇り(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です』
    (new Weather('tokyo'))->GetWeatherMessage(
        (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))
    )
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
