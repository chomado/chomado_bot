<?php
require_once(__DIR__ . '/twitteroauth/twitteroauth.php'); // OAuth
require_once(__DIR__ . '/class/config.php');        // class Config
require_once(__DIR__ . '/class/chat.php'); // docomo対話APIのクラス
require_once(__DIR__ . '/class/chatcontext.php');

$param = [];

// 最終投稿IDを取得
if(@file_exists(__DIR__ . '/tweet_content_data_list/last_id.txt')) {
    if($since_id = file_get_contents(__DIR__ . '/tweet_content_data_list/last_id.txt')) {
        $param['since_id'] = $since_id;
    }
    unset($since_id);
}

// ファイルの行をランダムに抽出
$face_list = file(__DIR__ . '/tweet_content_data_list/face_list.txt');
shuffle($face_list);
$index = 0;

// Twitterに接続
$config = Config::getInstance();
$connection = new TwitterOAuth(
    $config->getTwitterConsumerKey(),
    $config->getTwitterConsumerSecret(),
    $config->getTwitterAccessToken(),
    $config->getTwitterAccessTokenSecret()
);

// リプライを取得
echo "Twitter に問合せ中: " . json_encode($param) . "\n";
$res = $connection->get('statuses/mentions_timeline', $param);

if(is_array($res) && !empty($res))
{
    // 最終投稿IDを書き込む
    file_put_contents(__DIR__ . '/tweet_content_data_list/last_id.txt', $res[0]->id_str);

    echo count($res) . "件の新着\n";

    $chat_context = new ChatContext();

    foreach ($res as $re) 
    {
        $param = [];

        echo "届いたメッセージ:\n";
        printf("[@%s] %s - %s\n", $re->user->screen_name, $re->user->name, $re->text);

        // もし自分自身宛てだったら無視する.(無限ループになっちゃうから)
        if (strtolower($re->user->screen_name) === strtolower($config->getTwitterScreenName())) {
            continue;
        }

        // docomoAPIに送信する本文から余計なものを取り除く
        $docomo_send_text = trim(preg_replace('/@[a-z0-9_]+/i', '', $re->text));
        
        // ツイート本文
        $chat = new Chat(
            $config->getDocomoDialogueApiKey(),
            $chat_context->getContextId($re->user->screen_name),
            $chat_context->getMode($re->user->screen_name),
            $re->user->name,
            $docomo_send_text
        );
        $message = sprintf('%s %s%s'
            , $chat->ResText()
            , $face_list[$index]
            , PHP_EOL
            );

        $param['status'] = sprintf('@%s %sさん%s%s'
            , $re->user->screen_name
            , trim(preg_replace('!([@＠#＃.]|://)!u', " $1 ", $re->user->name))
            , PHP_EOL
            , $message
            );

        $param['in_reply_to_status_id'] = $re->id_str;

        echo "ツイッターに送るパラメータ:\n";
        echo json_encode($param, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";

        // 投稿
        $connection->post('statuses/update', $param);

        echo "ツイッターに返信を投稿しました\n";

        $chat_context->setContext(
            $re->user->screen_name,
            $chat->GetChatContextId(),
            $chat->GetChatMode()
        );
        $index = ($index + 1) % count($face_list);
    }
} else {
    echo "新着なしまたはエラー\n";
}
