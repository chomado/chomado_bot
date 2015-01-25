<?php
require_once dirname(__FILE__) . '/../static_data/WeatherDictionary.php';// 天気情報クラス

class Weather
{
	private $city; // String. 天気情報欲しい都市
	private $info; // [String]. GetWeather()で得られる(元JSONの)情報が入る配列
	private $weather; // new天気の英語日本語の辞書オブジェクトが入る

	public function __construct($city)
	{
		$this->city 	= $city;
		$this->info 	= $this->GetWeather();// API呼び出しを1回で済ませるためにここでgetしておく
		$this->weather 	= new WeatherDictionary();
	}
	// 華氏→摂氏変換関数
	private function FtoC($f)
	{
		return round(($f - 32) * 0.555, 1);
	}

	// yahoo の天気予報 API から引っ張ってくる
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
		//FIXME: file_get_contentsはサーバ側の理由等で当然失敗するかもしれない
		//FIXME: allow_url_fopenが死んでるかもしれない
		return json_decode(file_get_contents($query_uri))->query->results->channel->item;
	}

	// 現在の天気
	private function GetCondition()
	{
		return [
			'weather'	=> $this->weather->GetJapanese($this->info->condition->code, $this->info->forecast[1]->text)
			, 'temp'	=> $this->FtoC($this->info->condition->temp)
		];
	}
	// 明日の天気情報
	private function GetTomorrow()
	{
		return [
			'weather'	=> $this->weather->GetJapanese($this->info->forecast[1]->code, $this->info->forecast[1]->text)
			, 'high'	=> $this->FtoC($this->info->forecast[1]->high)
			, 'low'		=> $this->FtoC($this->info->forecast[1]->low)
		];
	}

	// 文章成形. 『東京の現在(21:15)の天気は晴れ(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です』
	public function GetWeatherMessage($time)
	{
		$now 	  = $this->GetCondition();
		$tomorrow = $this->GetTomorrow();

		$message = sprintf(
			'東京の現在(%s)の天気は%s(%.1f℃)です。%s明日は%s(最高%.1f℃/最低%.1f℃)です%s'
            , $time->GetTime()->format('H:i')
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
