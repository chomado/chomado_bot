chomado_bot
===========

Twitter の [@chomado_bot|https://twitter.com/chomado_bot] を作っていきます

## 機能

### ver. 0.0.1

* list.txt から ランダムにひとつ選んでツイートする
* cron には 毎時15分 (00:15, 01:15, ..) に叩かれるように設定してある

サンプル: 

> (((o(*ﾟ▽ﾟ*)o))) ちょまどbot  

[https://twitter.com/chomado_bot/status/537570121653309440]

課題
* 発言内容が少なすぎて怒られる (発言がかぶるから)

### ver. 0.0.2

* + 発言内容が被らないように現在時刻(JST)も一緒に呟く [new!]

サンプル: 

> (((o(*ﾟ▽ﾟ*)o))) ちょまどbot  
> 2014/11/30 17:15

[https://twitter.com/chomado_bot/status/538969441724162048]

### ver. 0.1.0

* + 現在の天気と明日の天気をつぶやく [new!]

サンプル: 

> (((o(*ﾟ▽ﾟ*)o))) ちょまどbot   
> 東京の現在(12/01 00:52)の天気はLight Rain(13.9℃)です.
明日はThunderstormsで, 最高気温は17.8℃, 最低気温は7.2℃ です.

[https://twitter.com/chomado_bot/status/539084512504721408]

## 今後やりたい機能

* リプライ
* メンションで任意の都市の天気を取得