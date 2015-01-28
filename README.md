chomado_bot
===========

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/chomado/chomado_bot?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Twitter の [@chomado_bot](https://twitter.com/chomado_bot) を作っていきます

## 機能 (追加されたのが新しい順. 更新履歴みたいな感じ)

### ver. 0.4.0
2015/1/24

* docomo の対話API を使って, リプライでの対話機能をつけた.
* [雑談対話 | docomo Developer support | NTTドコモ](https://dev.smt.docomo.ne.jp/?p=docs.api.page&api_docs_id=5#tag01)

> @junjiru じゅんじさん  
> BLは好きですね  
>  ┌（┌ \*ﾟ▽ﾟ\*）┐  
  
[https://twitter.com/chomado_bot/status/560323916413956096](https://twitter.com/chomado_bot/status/560323916413956096)
  
### ver. 0.3.0
2015/1/22

* リプライ機能付けた

> @chomado ちょまど @ bot開発楽しいさん  
> わーい(((o(\*ﾟ▽ﾟ\*)o)))わーい!(((o(\*ﾟ▽ﾟ\*)o)))  
  
### ver. 0.2.0
2015/1/20

* 『今日は第n週目のa曜です。今年のx%が経過しました。』という,
    - 今年入ってからどれくらいの月日が経ったのかのパーセント
    - 今年入ってから累計第何週

> ちょまどbot!(\*ﾟ▽ﾟ\* っ)З    
> 今日2015/01/20は第04週目の火曜です。今年の5.2%が経過しました。    
> 東京の現在(20:57)の天気は晴れ(6.1℃)です。    
> 明日はPM Rainで、最高5.6℃、最低3.9℃です     

[https://twitter.com/chomado_bot/status/557507258012020736]

### ver. 0.1.2
2014/12/04

* 英語を全部日本語にした. (天気の英語→日本語, の辞書ファイルを作った)

サンプル:

> ┌（┌ \*ﾟ▽ﾟ\*）┐ チョマドォ...   
> 東京の現在(12/04 21:47)の天気は軽い雨(10℃)です.   
> 明日は午前にわか雨で, 最高気温は11.1℃, 最低気温は2.8℃ です.

[https://twitter.com/chomado_bot/status/540487458509447168]

### ver. 0.1.1
2014/12/03

* 英語を日本語にするために, 全英語のYahoo!WeatherAPIから,
* 最初から全部日本語で配信しているLivedoor天気APIに乗り換えた.
* ただしこのAPIは現在の気温が取得できないし, 今日だけでなく明日の気温もNULLが入る場合もあるらしい
* ので, ちょっとこれはいかんかもしれん. 翻訳面倒くさいから最初から日本語のものにと逃げることはできなさそうだ

サンプル:

> (\*ﾟ▽ﾟ\* っ)З ちょまぎょ!   
> 東京都 東京 の天気(12/03 22:56現在) は晴れ(℃)です.   
> 明日は曇のち雨で, 最高気温は10℃, 最低気温は6℃ です.

[https://twitter.com/chomado_bot/status/540142614448594945]

### ver. 0.1.0
2014/12/01

* + 現在の天気と明日の天気をつぶやく [new!]

サンプル: 

> (((o\*ﾟ▽ﾟ\*)o))) ちょまどbot   
> 東京の現在(12/01 00:52)の天気はLight Rain(13.9℃)です.   
> 明日はThunderstormsで, 最高気温は17.8℃, 最低気温は7.2℃ です.

[https://twitter.com/chomado_bot/status/539084512504721408]

### ver. 0.0.2
2014/11/30

* + 発言内容が被らないように現在時刻(JST)も一緒に呟く [new!]

サンプル: 

> (((o(\*ﾟ▽ﾟ\*)o))) ちょまどbot  
> 2014/11/30 17:15

[https://twitter.com/chomado_bot/status/538969441724162048]

### ver. 0.0.1
2014/11/26

* list.txt から ランダムにひとつ選んでツイートする
* cron には 毎時15分 (00:15, 01:15, ..) に叩かれるように設定してある

サンプル: 

> (((o(\*ﾟ▽ﾟ\*)o))) ちょまどbot  

[https://twitter.com/chomado_bot/status/537570121653309440]

課題
* 発言内容が少なすぎて怒られる (発言がかぶるから)


## 今後やりたい機能

* メンションで任意の都市の天気を取得
