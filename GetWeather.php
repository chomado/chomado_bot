<?php
// 天気情報クラス
class GetWeather
{
	private $city; // int スポットコードが入る
	private $info; // [string] APIから取ってきたJSONを配列にしたものが入る
	
	public function __construct($city)
	{
		// 現在は東京固定. いずれ他の都市にも対応したい
		$this->city = $city === 'tokyo' ? 130010 : 090010; // 090010 は栃木県宇都宮市(私の故郷)
		$this->info = $this->GetWeather();// API呼び出しを1回で済ませるためにここでgetしておく
	}

	// ライブドアの天気予報 API から引っ張ってくる
	private function GetWeather()
	{
		$data = json_decode(mb_convert_encoding(file_get_contents(
			'http://weather.livedoor.com/forecast/webservice/json/v1?city=' 
			. $this->city
			), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'));
		var_dump($data);
		return $data;
	}

	// 現在の天気
	public function GetCondition()
	{
		$now = [
			'title'		=> $this->info->title
			, 'weather'	=> $this->info->forecasts[0]->telop
			, 'temp'	=> $this->info->forecasts[0]->temperature->max->celsius
		];
		var_dump($now['weather']);
		var_dump($now['temp']);
		return $now;
	}
	// 明日の天気情報
	public function GetTomorrow()
	{
		$tomorrow = [
			'weather'	=> $this->info->forecasts[1]->telop
			, 'high'	=> $this->info->forecasts[1]->temperature->max->celsius
			, 'low'		=> $this->info->forecasts[1]->temperature->min->celsius
		];
		return $tomorrow;
	}
}