<?php
namespace bot;

/**
 * 設定読み込み・取得用クラス
 */
class Config {
    /**
     * コンフィグファイルへの相対パス
     */
    const CONFIG_FILE_PATH = '../config/config.ini';

    /**
     * Singleton instance
     *
     * @var object (self)
     */
    private static $instance;

    /**
     * コンフィグデータを保持する変数
     *
     * @var array
     */
    private $data;

    /**
     * このクラスのインスタンスを取得する
     *
     * @return object (self)
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     */
    private function __construct() {
        $this->load();
    }

    /**
     * Twitter の OAuth コンシューマキーを取得
     *
     * @return string
     */
    public function getTwitterConsumerKey() {
        return $this->get('twitter', 'consumer_key');
    }

    /**
     * Twitter の OAuth コンシューマシークレットを取得
     *
     * @return string
     */
    public function getTwitterConsumerSecret() {
        return $this->get('twitter', 'consumer_secret');
    }

    /**
     * Twitter の OAuth アクセストークンを取得
     *
     * @return string
     */
    public function getTwitterAccessToken() {
        return $this->get('twitter', 'access_token');
    }

    /**
     * Twitter の OAuth アクセストークン（シークレット）を取得
     *
     * @return string
     */
    public function getTwitterAccessTokenSecret() {
        return $this->get('twitter', 'access_token_secret');
    }

    /**
     * bot の screen_name を取得
     *
     * @return string
     */
    public function getTwitterScreenName() {
        return $this->get('twitter', 'screen_name');
    }

    /**
     * bot の owner_screen_name を取得
     *
     * メンテしてる人の@名前. 何かあった時にこの人にリプライ飛ばす仕様にする. (@誰 エラー何件あったよ)
     *
     * @return string
     */
    public function getTwitterOwnerScreenName() {
        return $this->get('twitter', 'owner_screen_name');
    }
    /**
     * docomo雑談対話APIのAPIKEYを取得
     *
     * @return string
     */
    public function getDocomoDialogueApiKey() {
        return $this->get('docomo', 'dialogue_api_key');
    }

    /**
     * 設定ファイルを読み込む
     */
    private function load() {
        $ini_file_path = __DIR__ . DIRECTORY_SEPARATOR . self::CONFIG_FILE_PATH;
        if(!@file_exists($ini_file_path)) {
            throw new \Exception('Configuration file does not exist');
        }

        $ini = @parse_ini_file($ini_file_path, true);
        if($ini === false) {
            throw new \Exception('Configuration file format is broken.');
        }
        $this->data = $ini;
    }

    /**
     * 設定ファイルの項目を取得する
     *
     * @param string $section   iniファイル中のセクション("[hoge]" の部分)
     * @param string $key       iniファイル中の設定キー
     * @return string 設定の値。該当するものがないときは false
     */
    private function get($section, $key) {
        if(is_array($this->data) &&
           isset($this->data[$section]) &&
           is_array($this->data[$section]) &&
           array_key_exists($key, $this->data[$section]))
        {
            return $this->data[$section][$key];
        }
        return false;
    }
}
