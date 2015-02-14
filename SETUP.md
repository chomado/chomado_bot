chomado_bot の動作環境
======================

* PHP 5.4 以上
* Unix系OS
* コマンドラインシェル
* cronの登録権

chomado_bot の初期設定方法
==========================

## Twitterアカウントの設定

1. Twitterで新しいbotになるアカウントを登録します。

2. bot用のアクセストークンを4種類取得します。
    1. https://apps.twitter.com/ から新しいアプリを生成して、 Consumer Key, Consumer Secret を得ます。アクセスレベルとしては Read and Write が必要です。

    2. アプリケーション設定の「Keys and Access Tokens」タブの下方に「Create my access token」というものがあります。そこをクリックすると Access Token, Access Token Secret が得られます。

3. このレポジトリの config ディレクトリ内の config.ini.sample を config.ini にコピーし、エディタで開きます。
    * `[twitter]` セクション内の5つの項目(取得した4種類のアクセストークンとbotのscreen_name `(@id)`)を変更します。

## docomo雑談対話APIキーの設定

(あとで)

## 依存ライブラリの準備

chomado_botが依存するライブラリはレポジトリに含まれていないため、次の手順で設定します。

1. シェルを開き、レポジトリのディレクトリへ移動します。

2. 次のコマンドを実行します `make all`
    * make が実行できない環境では次の手順で実行します
        1. `curl -sS https://getcomposer.org/installer | php`
            * cURL が入っていない環境では: `php -r "readfile('https://getcomposer.org/installer');" | php`
            * 詳しくは: https://getcomposer.org/download/
        2. `php composer.phar install`

chomado_bot の実行方法
======================

(あとで)

