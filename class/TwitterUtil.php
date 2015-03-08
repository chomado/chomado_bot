<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twitterを利用するためのユーティリティ関数
 */
class TwitterUtil
{
    /**
     * ツイートを投稿する関数
     *
     * @param   object  $connection     投稿に使用する TwitterOAuth のインスタンス
     * @param   array   $param          Twitter に送信するパラメータ
     * @param   int     $retryLimit     最大再試行回数
     * @return  bool    投稿に成功すれば true、失敗すれば false
     */
    public static function postTweet(TwitterOAuth $connection, array $param, $retryLimit = 3)
    {
        Log::info("Twitter に tweet を POST します:");
        Log::info($param);
        for ($retry = 0; $retry < $retryLimit; ++$retry) {
            if ($retry > 0) {
                sleep(1);
            }
            $result = $connection->post('statuses/update', $param);
            if (is_object($result) && isset($result->id_str) && isset($result->text)) {
                Log::success("Tweet を投稿しました");
                Log::success(['id' => $result->id_str, 'text' => $result->text]);
                return true;
            }
            Log::warning("Tweet の投稿に失敗しました");
        }
        Log::error("Tweet を投稿できませんでした");
        Log::error($param);
        return false;
    }
}
