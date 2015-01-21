<?php
/**
*  天気辞書クラス
*/
class WeatherDictionary
{
    function __construct()
    {
    }
    
    private $dictionary = [
        "tornado"                 => "竜巻",
        "tropical storm"          => "台風",
        "hurﬁcane"                => "ハリケーン",
        "severe thunderstorms"    => "激しい雷雨",
        "thunderstorms"           => "雷雨",
        "mixed rain and snow"     => "雪混じりの雨",
        "mixed rain and sleet"    => "みぞれ混じりの雨",
        "mixed snow and sleet"    => "みぞれ混じりの雪",
        "freezing drizzle"        => "着氷性の霧雨",
        "drizzle"                 => "霧雨",
        "freezing rain"           => "着氷性の雨",
        "showers"                 => "にわか雨",
        "snow flurries"           => "雪の突風",
        "light snow showers"      => "時々雪",
        "blowing snow"            => "吹雪",
        "snow"                    => "雪",
        "hail"                    => "雹",
        "sleet"                   => "みぞれ",
        "dust"                    => "ほこり",
        "foggy"                   => "霧",
        "haze"                    => "靄",
        "seoky"                   => "埃っぽい",
        "blustedy"                => "荒れ模様",
        "windy"                   => "強風",
        "cold"                    => "寒い",
        "cloudy"                  => "曇り",
        "mostly cloudy"           => "うす曇り",
        "partly cloudy"           => "ところにより曇り",
        "clear"                   => "快晴",
        "mostly clear"            => "おおむね快晴",
        "sunny"                   => "おおむね晴れ",
        "fair"                    => "晴れ",
        "mixed rain and hail"     => "雨と雹",
        "hot"                     => "暑い",
        "isolated thunderstorms"  => "局地的に雷雨",
        "scattred thundestorms"   => "ところにより雷雨",
        "scattered showwrs"       => "ところによりにわか雨",
        "scattered snow showers"  => "吹雪",
        "heavy snow"              => "大雪",
        "thundershowers"          => "雷雨",
        "snow shwers"             => "吹雪",
        "isolated thundershowers" => "ところにより雷雨",
        "smoky"                   => "黒霧",
        "am showers"              => "午前にわか雨",
        "pm showers"              => "午後にわか雨",
        "light rain"              => "軽い雨",
    ];

    // 引数:"Sunny" → 返り値: "晴れ"
    // という, String を返す. もし存在しない英単語が来たら, 英語のまま返す. (NULLは返さない)
    public function GetJapanese($english)
    {
        $key = strtolower($english);
        return isset($this->dictionary[$key]) ? $this->dictionary[$key] : $english;
    }
}
