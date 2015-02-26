<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot;

use Curl\Curl;

/**
 * docomo の雑談対話APIを使うためのクラス
 */
class Chat
{
    /**
     * API呼び出し結果オブジェクト
     *
     * @var object
     */
    private $response;

    /**
     * コンストラクタ
     *
     * @param string $apikey    docomoAPIキー
     * @param string $context   会話のコンテキストID(API仕様参照)
     * @param string $mode      会話のモード(API仕様参照
     * @param string $nickname  会話している人間側の名前
     * @param string $text      人間側の入力テキスト
     * @see getData()
     */
    public function __construct($apikey, $context, $mode, $nickname, $text)
    {
        $this->response = $this->getData($apikey, $context, $mode, $nickname, $text);
    }

    /**
     * docomoの対話APIを叩いてレスポンスを貰ってくる
     *
     * @param string $apikey    docomoAPIキー
     * @param string $context   会話のコンテキストID(API仕様参照)
     * @param string $mode      会話のモード(API仕様参照
     * @param string $nickname  会話している人間側の名前
     * @param string $text      人間側の入力テキスト
     * @return stdClass         レスポンスのJSONをデコードしたオブジェクト
     * @throws \Exception       サーバとの通信に失敗した場合
     */
    private function getData($apikey, $context, $mode, $nickname, $text)
    {
        $userData = [
            'utt'       => (string)$text,
            'context'   => (string)$context,
            'nickname'  => (string)$nickname,
            'mode'      => (string)$mode,
        ];
        $url = sprintf(
            'https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s',
            rawurlencode($apikey)
        );
        Log::info("docomo対話APIを呼び出します");
        Log::info("URL: " . $url);
        Log::info("パラメータ:");
        Log::info($userData);

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $ret = $curl->post($url, json_encode($userData));
        if ($curl->error) {
            Log::error(sprintf(
                "docomo対話APIの呼び出しに失敗しました: %d: %s",
                $curl->error_code,
                $curl->error_message
            ));
            throw new \Exception('docomo dialogue error: ' . $curl->error_code . ': ' . $curl->error_message);
        }
        Log::info("docomoからのデータ:");
        Log::info($ret);
        if (is_object($ret) && isset($ret->utt)) {
            if ($ret->utt == '') {
                Log::warning("  docomo 指示文章が空です");
            } else {
                Log::success("  docomo 指示文章: " . $ret->utt);
            }
            return $ret;
        }
        Log::error("docomoから受け取ったデータが期待した形式ではありません:");
        Log::error($ret);
        throw new \Exception('Received an unexpected data from docomo server');
    }

    /**
     * ユーザに返すべき会話内容を取得する
     *
     * @return string 会話内容
     */
    public function resText()
    {
        $message = sprintf('%s%s', $this->response->utt, PHP_EOL);
        return $message;
    }

    /**
     * 応答に含まれるコンテキストIDを取得する
     *
     * @return string
     */
    public function getChatContextId()
    {
        return isset($this->response->context) ? $this->response->context : '';
    }

    /**
     * 応答に含まれる会話モードを取得する
     *
     * @return string "dialog" or "srtr" ( or "" )
     */
    public function getChatMode()
    {
        return isset($this->response->mode) ? $this->response->mode : '';
    }
}
