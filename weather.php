<?php
require_once('static_data/WeatherDictionary.php');
// 天気情報クラス
class Weather
{
	private $city; // String. 天気情報欲しい都市
	private $info; // [String]. GetWeather()で得られる(元JSONの)情報が入る配列
	private $weather; // new天気の英語日本語の辞書オブジェクトが入る

	public function __construct($city)
	{
		$this->city = $city;
		$this->info = $this->GetWeather();// API呼び出しを1回で済ませるためにここでgetしておく
		$this->weather = new WeatherDictionary();
	}
	// 華氏→摂氏変換関数
	private function FtoC($f)
	{
		return round(($f - 32) * 0.555, 1);
	}

	// yahoo の天気予報 API から引っ張ってくる
	private function GetWeather()
	{
		return json_decode(file_get_contents(
			'https://query.yahooapis.com/v1/public/yql?q=select%09*%20from%20%09weather.forecast%20%20where%20%09woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22' 
			. $this->city
			. '%2C%20jp%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'
			))->query->results->channel->item;
	}

	// 現在の天気
	private function GetCondition()
	{
		return [
			'weather'	=> $this->weather->GetJapanese($this->info->condition->text)
			, 'temp'	=> $this->FtoC($this->info->condition->temp)
		];
	}
	// 明日の天気情報
	private function GetTomorrow()
	{
		return [
			'weather'	=> $this->weather->GetJapanese($this->info->forecast[1]->text)
			, 'high'	=> $this->FtoC($this->info->forecast[1]->high)
			, 'low'		=> $this->FtoC($this->info->forecast[1]->low)
		];
	}

	// 文章成形. 『東京の現在(21:15)の天気は晴れ(6.1℃)です。明日はPM Rainで、最高5.6℃、最低3.9℃です』
	public function GetWeatherMessage($time)
	{
		$now 	  = $this->GetCondition();
		$tomorrow = $this->GetTomorrow();

		$message = '東京の現在('
            . $time->GetTime()->format('H:i')
            . ')の天気は'
            . $now['weather']
            . '('
            . $now['temp']
            . '℃)です。'
            . PHP_EOL
            . '明日は'
            . $tomorrow['weather']
            . 'で、'
            . '最高'
            . $tomorrow['high']
            . '℃、最低'
            . $tomorrow['low']
            . '℃です'
            . PHP_EOL;
        return $message;
	}

}