<?php
namespace bot;

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
     * @see GetData()
     */
    public function __construct($apikey, $context, $mode, $nickname, $text)
    {
        $this->response = $this->GetData($apikey, $context, $mode, $nickname, $text);
    }

    /**
     * docomoの対話APIを叩いてレスポンスを貰ってくる
     *
     * @param string $apikey    docomoAPIキー
     * @param string $context   会話のコンテキストID(API仕様参照)
     * @param string $mode      会話のモード(API仕様参照
     * @param string $nickname  会話している人間側の名前
     * @param string $text      人間側の入力テキスト
     * @return object レスポンスのJSONをデコードしたオブジェクト
     */
    private function GetData($apikey, $context, $mode, $nickname, $text)
    {
        $user_data = [
            'utt'       => self::sjisSafe((string)$text),
            'context'   => (string)$context,
            'nickname'  => self::sjisSafe((string)$nickname),
            'mode'      => (string)$mode,
        ];
        $url = sprintf(
            'https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s',
            rawurlencode($apikey)
        );
        $options = [
            'http' => [
                'method'    => 'POST',
                'header'    => "Content-type: application/json",
                'content'   => json_encode($user_data),
            ],
        ];

        Log::info("docomo対話APIを呼び出します");
        Log::info("URL: " . $url);
        Log::info("パラメータ:");
        Log::info($user_data);
        
        $response = file_get_contents($url, false, stream_context_create($options));
        $ret = @json_decode($response);
        if($response === false) {
            Log::error("docomo対話APIの呼び出しに失敗しました");
        } else {
            Log::info("docomoからのデータ:");
            Log::info($response);
            if(is_object($ret) && isset($ret->utt) && $ret->utt != '') {
                Log::success("  docomo 指示文章: " . $ret->utt);
            }
        }
        return $ret;
    }

    /**
     * ユーザに返すべき会話内容を取得する
     *
     * @return string 会話内容
     */
    public function ResText()
    {
        $message = sprintf('%s%s', $this->response->utt, PHP_EOL);
        return $message;
    }

    /**
     * 応答に含まれるコンテキストIDを取得する
     *
     * @return string
     */
    public function GetChatContextId() {
        return isset($this->response->context) ? $this->response->context : '';
    }

    /**
     * 応答に含まれる会話モードを取得する
     *
     * @return string "dialog" or "srtr" ( or "" )
     */
    public function GetChatMode() {
        return isset($this->response->mode) ? $this->response->mode : '';
    }

    /**
     * 与えられた文字列を Shift-JIS で収まる範囲に変換する
     *
     * docomo APIがどうも Shift-JIS で収まらないものを与えると Bad Request で死ぬようなので
     * とりあえず SJIS で収まるもののみを送るようにしてみる
     *
     * @param string $text 変換対象文字列
     * @return string
     */
    private static function sjisSafe($text) {
        mb_substitute_character(0x3013); // 変換できない文字をゲタにする
        return mb_convert_encoding(
            mb_convert_encoding($text, 'CP932', 'UTF-8'),
            'UTF-8',
            'CP932'
        );
    }
}
