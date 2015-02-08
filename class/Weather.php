<?php
namespace bot;
use bot\weather\Dictionary;

/**
 * 天気情報取得クラス
 */
class Weather
{
    /** @var string 天気情報を取得する対象の都市 */
	private $city;

    /**
     * @var stdClass 取得した天気情報
     * @see GetWeather()
     */
	private $info;

    /**
     * コンストラクタ
     *
     * @param   string  $city   対象の都市の名前(例: tokyo)
     */
	public function __construct($city)
	{
		$this->city 	= $city;
		$this->info 	= $this->GetWeather();// API呼び出しを1回で済ませるためにここでgetしておく
	}

    /**
     * 華氏から摂氏に変換する
     * 
     * @param   float   $f  華氏温度
     * @return  float       摂氏温度
     */
	private function FtoC($f)
	{
        return ((double)$f - 32) * 5 / 9;
	}

    /**
     * 現在の天気の情報を問い合わせる
     *
     * @todo: クエリは当然失敗するかもしれないのでエラー処理が必要
     * @todo: YQLに埋め込まれるパラメータのエスケープ方法が不明
     */
	private function GetWeather()
	{
		$parameters = [
			'q' 		=> sprintf(
							'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="%s")'
							, $this->city), //FIXME: cityのエンコード方法は?
			'format' 	=> 'json',
			'env' 		=> 'store://datatables.org/alltableswithkeys',
		];
		$query_uri = 'https://query.yahooapis.com/v1/public/yql?'
					. http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
		return json_decode(file_get_contents($query_uri))->query->results->channel->item;
	}

    /**
     * 現在の天気を取得
     *
     * @return array
     */
	private function GetCondition()
	{
		return [
			'weather'	=> Dictionary::GetJapanese($this->info->condition->code, $this->info->forecast[1]->text)
			, 'temp'	=> $this->FtoC($this->info->condition->temp)
		];
	}

    /**
     * 明日の天気を取得
     *
     * @return array
     */
	private function GetTomorrow()
	{
		return [
			'weather'	=> Dictionary::GetJapanese($this->info->forecast[1]->code, $this->info->forecast[1]->text)
			, 'high'	=> $this->FtoC($this->info->forecast[1]->high)
			, 'low'		=> $this->FtoC($this->info->forecast[1]->low)
		];
	}

    /**
     * 天気情報を成形して取得する
     *
     * @param   \DateTime   $time   現在の日時
     * @return  string              例: 東京の現在(21:15)の天気は晴れ(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です
     *
     * @todo    "東京" がハードコーディングされている
     * @todo    APIで取得してきた情報は現在時間のものではない
     * @todo    現在時間なら $time はそもそも不要
     */
	public function GetWeatherMessage($time)
	{
		$now 	  = $this->GetCondition();
		$tomorrow = $this->GetTomorrow();

		$message = sprintf(
			'東京の現在(%s)の天気は、%s(%.1f℃)です。%s明日は%sで、最高気温%.1f℃、最低気温%.1f℃です。%s'
            , $time->format('m/d H:i')
            , $now['weather']
            , $now['temp']
            , PHP_EOL
            , $tomorrow['weather']
            , $tomorrow['high']
            , $tomorrow['low']
            , PHP_EOL);
        return $message;
	}
}
