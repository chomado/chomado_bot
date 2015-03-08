<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

use Abraham\TwitterOAuth\TwitterOAuth;
use chomado\bot\Config;
use chomado\bot\DateTime as MyDateTime;
use chomado\bot\Log;
use chomado\bot\RandomSentenceList;
use chomado\bot\TwitterUtil;
use chomado\bot\format\WeatherFormatter;
use chomado\bot\weather\yahoocom\Client as WeatherClient;

// bootstrap
require_once(__DIR__ . '/vendor/autoload.php');
Log::setErrorHandler();

// ファイルの行をランダムに抽出
$randomComments = new RandomSentenceList(__DIR__ . '/tweet_content_data_list/list.txt');
Log::trace("list.txtは" . count($randomComments) . "行です");

$timeZoneJst = new DateTimeZone('Asia/Tokyo');
$now = new MyDateTime('now', $timeZoneJst);

// 天気情報
$weather = (new WeatherClient('tokyo'))->query();
$formattedWeather = WeatherFormatter::formatForWeatherTweet($weather, '東京', $timeZoneJst);

// 呟く文成形 
// ============================================================================
// (*ﾟ▽ﾟ* っ)З ちょまぎょ!
//
// 現在時刻は17:55です。
// 東京の17:30現在の天気は、にわか雨(7.2℃)です。
// 明日はところにより曇り(昼)で、最高気温12.8℃、最低気温3.3℃です。
// ============================================================================
$message = sprintf(
    "%s\n\n%s\n%s",
    $randomComments->get(),
    '現在時刻は' . $now->format('H:i') . 'です。',
    $formattedWeather
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
