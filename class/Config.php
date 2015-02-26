<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;

/**
 * 設定読み込み・取得用クラス
 */
class Config
{
    /**
     * コンフィグファイルへの相対パス
     */
    const CONFIG_FILE_PATH = '../config/config.ini';

    /**
     * Singleton instance
     *
     * @var self
     */
    private static $instance;

    /**
     * コンフィグデータを保持する変数
     *
     * @var string[][]
     */
    private $data;

    /**
     * このクラスのインスタンスを取得する
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     */
    private function __construct()
    {
        $this->load();
    }

    /**
     * Twitter の OAuth コンシューマキーを取得
     *
     * @return string
     */
    public function getTwitterConsumerKey()
    {
        return $this->get('twitter', 'consumer_key');
    }

    /**
     * Twitter の OAuth コンシューマシークレットを取得
     *
     * @return string
     */
    public function getTwitterConsumerSecret()
    {
        return $this->get('twitter', 'consumer_secret');
    }

    /**
     * Twitter の OAuth アクセストークンを取得
     *
     * @return string
     */
    public function getTwitterAccessToken()
    {
        return $this->get('twitter', 'access_token');
    }

    /**
     * Twitter の OAuth アクセストークン（シークレット）を取得
     *
     * @return string
     */
    public function getTwitterAccessTokenSecret()
    {
        return $this->get('twitter', 'access_token_secret');
    }

    /**
     * bot の screen_name を取得
     *
     * @return string
     */
    public function getTwitterScreenName()
    {
        return $this->get('twitter', 'screen_name');
    }

    /**
     * bot のメンテナの screen_name を取得
     *
     * メンテしてる人の@id.
     *
     * @return string
     */
    public function getTwitterOwnerScreenName()
    {
        return $this->get('twitter', 'owner_screen_name');
    }
    /**
     * docomo雑談対話APIのAPIKEYを取得
     *
     * @return string
     */
    public function getDocomoDialogueApiKey()
    {
        return $this->get('docomo', 'dialogue_api_key');
    }

    /**
     * 設定ファイルを読み込む
     *
     * @throw \Exception    設定ファイルが存在しないときや壊れているときに例外を投げる
     */
    private function load()
    {
        $iniFilePath = __DIR__ . '/' . self::CONFIG_FILE_PATH;
        if (!@file_exists($iniFilePath)) {
            throw new \Exception('Configuration file does not exist');
        }

        $ini = @parse_ini_file($iniFilePath, true);
        if ($ini === false) {
            throw new \Exception('Configuration file format is broken.');
        }
        $this->data = $ini;
    }

    /**
     * 設定ファイルの項目を取得する
     *
     * @param string $section   iniファイル中のセクション("[hoge]" の部分)
     * @param string $key       iniファイル中の設定キー
     * @return string           設定の値
     * @throws \Exception       指定された設定が存在しないときは例外を投げる
     */
    private function get($section, $key)
    {
        if (is_array($this->data) &&
           isset($this->data[$section]) &&
           is_array($this->data[$section]) &&
           array_key_exists($key, $this->data[$section])) {
            return $this->data[$section][$key];
        }
        throw new \Exception("Configuration [{$section}] {$key} does not exist");
    }
}
