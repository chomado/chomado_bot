<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\weather\yahoocom;
use Curl\Curl;
use bot\Log;

/**
 * 天気情報取得クラス
 */
class Client
{
    /** Yahoo! YQL エンドポイント URL */
    const ENDPOINT_URL = 'https://query.yahooapis.com/v1/public/yql';

    /** @var string 問い合わせる都市名(e.g. tokyo) */
    private $city;

    /**
     * コンストラクタ
     *
     * @param string $city 問い合わせ対象の都市名
     */
    public function __construct($city) {
        $this->city = $city;
    }

    /**
     * 現在の天気の情報を問い合わせる
     *
     * @return \bot\weather\yahoocom\Response
     * @throws \Exception 問い合わせに失敗した場合
     */
    public function query() {
        $query_uri = self::buildQueryUrl([
            'q'      => $this->buildYql($this->city),
            'format' => 'json',
            'env'    => 'store://datatables.org/alltableswithkeys',
        ]);
        
        Log::info(__METHOD__ . ': Query URL: ' . $query_uri);
        $curl = new Curl();
        $curl->get($query_uri);
        if($curl->error) {
            $msg = 'YQL Query Error: ' . $curl->error_code . ': ' . $curl->error_message;
            Log::error(__METHOD__ . ': ' . $msg);
            Log::error($curl->raw_response);
            throw new \Exception($msg);
        }
        return new Response($curl->raw_response);
    }

    /**
     * YQLを構築する
     *
     * @param string $city "tokyo"のような都市名
     * @return string YQL
     */
    private static function buildYql($city) {
        return sprintf(
            'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="%s")',
            addslashes($city)
        );
    }

    /**
     * Yahoo! に問い合わせる際の URL を構築する
     *
     * @param array $parameters Yahoo!仕様の送信パラメータ
     * @return string URL
     */
    private static function buildQueryUrl(array $parameters) {
        return self::ENDPOINT_URL . '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}
