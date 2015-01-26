<?php
require_once(dirname(__FILE__) . '/../twitteroauth/twitteroauth.php'); // OAuth
require_once(dirname(__FILE__) . '/../static_data/botconfig.php'); // Twitterの各アクセスキー

// docomo の 雑談対話APIを使う
// new Chat($context, $nickname, $text)
    // 引数説明
    // $context;    // 会話のつながり保持のID. 
    // $nickname;   // 名前
    // $text;       // リプライで来た本文.

class Chat
{
    private $response;

    public function __construct($context, $nickname, $text)
    {
        $text_trimmed = trim(str_replace('@chomado_bot', '', $text));
        var_dump('送るテキスト:');
        var_dump($text_trimmed);
        $this->response = $this->GetData($context, $nickname, $text_trimmed);
    }

    // docomoの対話APIを叩いてレスポンスを貰ってくる (JSON形式)
    private function GetData($context, $nickname, $text)
    {
        $user_data = array(
            'utt'       => (string)$text,
            'context'   => (string)$context,
            'nickname'  => (string)$nickname,
        );
        $url        = sprintf(
                        'https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s'
                        , DOCOMO_CHAT_API_KEY);
        $options    = array('http' => array(
            'method'    => 'POST',
            'header'    => "Content-type: application/json",
            'content'   => json_encode($user_data),
        ));
        $contents = json_decode(file_get_contents($url, false, stream_context_create($options)));
        return $contents;
    }

    public function ResText()
    {
        var_dump('返ってきたデータ:' . PHP_EOL);
        var_dump($this->response);
        $message = sprintf('%s%s'
            , $this->response->utt
            , PHP_EOL
            );
        return $message;
    }

    public function GetChatContextId() {
        return isset($this->response->context) ? $this->response->context : '';
    }
}
