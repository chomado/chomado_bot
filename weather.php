<?php
// 天気情報クラス
class Weather
{
	private $city;
	// {天気, {最高気温, 最低気温}}
	private $info = [
		'weather'=>'0', ['high'=>'1', 'low'=>'2']
	];
	public function __construct($city)
	{
		$this->city = $city;
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
	// 華氏→摂氏変換関数
	private function FtoC($f)
	{
		return round(($f - 32) * 0.555, 1);
	}
	// 現在の天気
	public function GetCondition()
	{
		$now = $this->GetWeather($city)->condition;
		$info['weather'] 	= $now->text;
		$info[1]['high']	= $this->FtoC($now->temp);
		return $info;
	}
	// 明日の天気情報
	public function GetTomorrow()
	{
		$tomorrow = $this->GetWeather($city)->forecast[1];
		$info['weather']	= $tomorrow->text;
		$info[1]['high']	= $this->FtoC($tomorrow->high);
		$info[1]['low'] 	= $this->FtoC($tomorrow->low);
		return $info;
	}
}