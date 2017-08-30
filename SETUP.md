chomado_bot の動作環境
======================

* PHP 5.4 以上
* Unix系OS
* コマンドラインシェル
* cronの登録権

または

* Docker コンテナが実行できる環境


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

## Docker を利用した方法 ##

### 母艦が RHEL/CentOS/Scientific Linux/Oracle Linux 6 の場合 ###

1. 母艦で `sudo` できるようにします。
    * `visudo` で編集
    * 詳しくはおググりください。

2. 母艦に EPEL を追加します。
    * `$ curl -o epel-release-6-8.noarch.rpm 'http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm'`
    * `$ sudo yum localinstall epel-release-6-8.noarch.rpm`
    * `$ rm -f epel-release-6-8.noarch.rpm`

3. 母艦に `docker-io` パッケージを追加します。
    * `$ sudo yum install docker-io`
        - もしよくあるアドバイスに従って EPEL レポジトリを無効にしているのなら、 `$ sudo yum install install docker-io --enablerepo=epel` とかします。

4. このレポジトリをどこか適当な場所に clone して「chomado_bot の初期設定方法」に従って設定します。
    * 特に、 `config` ディレクトリ内の設定は重要です（この設定を適用した docker イメージが作られます）
    * もし、開発機が別にあってそこで動く環境にできているなら、そのままサーバに `rsync` や `scp` でコピーしてください。

5. Docker サービスを起動します。自動起動も設定します。
    * `$ sudo /sbin/service docker start`
    * `$ sudo /sbin/chkconfig docker on`

6. Docker イメージを作ります。
    * `$ make docker`
        - スクリプトの中で sudo するのでパスワードが求められたり求められなかったりします。
        - 大量のログと時間を消費した後、 `chomado/bot:latest` が出来上がります。
    
7. コンテナを自動起動する設定ファイルを作ります。
    * root で `/etc/rc.d/init.d/docker-chomadobot` とかに次の内容を記載します。
    * `chmod +x /etc/rc.d/init.d/docker-chomadobot` を忘れずに。

    ```sh
    #!/bin/sh
    # chkconfig:   2345 96 96
    # description: Docker container for chomado_bot
    ### BEGIN INIT INFO
    # Provides:       docker-chomadobot
    # Required-Start: $network docker
    # Required-Stop:
    # Should-Start:
    # Should-Stop:
    # Default-Start: 2 3 4 5
    # Default-Stop:  0 1 6
    # Short-Description: start and stop chomado_bot
    # Description:  Docker container for chomado_bot
    ### END INIT INFO
    
    . /etc/rc.d/init.d/functions
    
    start()
    {
        echo -n "Starting chomado_bot: "
        docker kill chomado_bot >/dev/null 2>&1
        docker rm chomado_bot >/dev/null 2>&1
        docker run -d --name=chomado_bot chomado/bot:latest && success || failure
        RETVAL=$?
        echo
        return $RETVAL
    }
    
    stop()
    {
        echo -n "Stopping chomado_bot: "
        docker stop chomado_bot && success || failure
        RETVAL=$?
        echo
        return $RETVAL
    }
    
    case "$1" in
      start)
        start
        ;;
      stop)
        stop
        ;;
      restart)
        start
        stop
        ;;
      *)
        echo "Usage: $0 {start|stop|restart}"
        ;;
    esac
    exit $RETVAL
    ```

8. 自動起動を設定します。
    * `$ sudo /sbin/chkconfig --add docker-chomadobot`
    * `$ sudo /sbin/chkconfig docker-chomadobot on`

9. 動作を開始します。
    * `$ sudo /sbin/service docker-chomadobot start`

それ以降の作業は:

1. ソースコードや設定を編集して、必要であればサーバ（母艦）にアップロードします。

2. Docker イメージを作ります。
    * `$ make docker`
        - ここで編集したソースが取り込まれます。

3. コンテナを再起動します。
    * `$ sudo /sbin/service docker-chomadobot restart`


### 母艦が RHEL/CentOS/Scientific Linux/Oracle Linux 7 の場合 ###

1. 母艦で `sudo` できるようにします。
    * `visudo` で編集
    * 詳しくはおググりください。

2. 母艦に `docker` パッケージを追加します。
    * `$ sudo yum install docker`
        - もし extras レポジトリが無効にされているなら、 `$ sudo yum install docker --enablerepo=extras` とかします。

3. このレポジトリをどこか適当な場所に clone して「chomado_bot の初期設定方法」に従って設定します。
    * 特に、 `config` ディレクトリ内の設定は重要です（この設定を適用した docker イメージが作られます）
    * もし、開発機が別にあってそこで動く環境にできているなら、そのままサーバに `rsync` や `scp` でコピーしてください。

4. Docker サービスを起動します。自動起動も設定します。
    * `$ sudo systemctl enable docker.service`
    * `$ sudo systemctl start docker.service`

5. Docker イメージを作ります。
    * `$ make docker`
        - スクリプトの中で sudo するのでパスワードが求められたり求められなかったりします。
        - 大量のログと時間を消費した後、 `chomado/bot:latest` が出来上がります。
    
6. コンテナを自動起動する設定ファイルを作ります。
    * root で `/etc/systemd/system/docker-chomadobot.service` とかに次の内容を記載します。

    ```
    [Unit]
    Description=Docker container for chomado_bot
    After=docker.service
    Requires=docker.service

    [Service]
    ExecStartPre=-/usr/bin/docker kill chomado_bot
    ExecStartPre=-/usr/bin/docker rm chomado_bot
    ExecStart=/usr/bin/docker run --name=chomado_bot chomado/bot:latest
    ExecStop=/usr/bin/docker stop chomado_bot

    [Install]
    WantedBy=multi-user.target
    ```

7. 自動起動を設定します。
    * `$ sudo systelctl enable docker-chomadobot`

9. 動作を開始します。
    * `$ sudo systemctl start docker-chomadobot`

それ以降の作業は:

1. ソースコードや設定を編集して、必要であればサーバ（母艦）にアップロードします。

2. Docker イメージを作ります。
    * `$ make docker`
        - ここで編集したソースが取り込まれます。

3. コンテナを再起動します。
    * `$ sudo systemctl restart docker-chomadobot`


### コンテナについて ###

* コンテナのベースイメージは CentOS 7 です。
* コンテナ内の `php` は [Software Collections](https://www.softwarecollections.org/en/) の [rh-php56](https://www.softwarecollections.org/en/scls/rhscl/rh-php56/) です。
* コンテナでは cron(crond) が動いています。 cron の設定は `/etc/cron.d/chomadocker` にあります。
* コンテナ内のユーザ名は `chomadocker` です。
* `runtime` ディレクトリはコンテナを再起動するたびに空になります。
    - docomo API を使ったチャットはその時点でコンテキストを忘れます。
    - どこまで返信したかを忘れるのでタイミングによっては二重に返信します。
        - 返信しまくる問題を回避するために 10 分以上昔のメンションは無視します。

### 起動中のコンテナに入り込む方法 ###

デバッグの必要上、コンテナに入りたければ次のようにします。

1. 実行中のコンテナ ID を取得します。
    * `$ sudo docker ps`
        - 起動していれば次のような出力が出ます。
            
            ```
            CONTAINER ID        IMAGE                       COMMAND                CREATED             STATUS              PORTS                  NAMES
            52b762ed4fd9        chomado/bot:latest          "/bin/sh -c '/usr/sb   2 hours ago         Up 2 hours                                 chomado_bot
            ```

        - この例では `52b762ed4fd9` がコンテナ ID です。

2. bash で入り込みます。（zsh とか当然入っていないので bash で頑張ってください）
    * `$ sudo docker exec -ti 52b762ed4fd9 /bin/bash`

3. 実行ユーザに化けたければこうします:
    * `# su - chomadocker`

ファイルを変更しても、そう遠くない未来（コンテナを restart したとき）に失われるのでログ調査程度にしておくのがおすすめです。

### その他 Docker に関する注意 ###

いろいろやると後々 docker の image が貯まってゴミ捨て場みたいになる可能性が高いので、時々掃除したほうがいいかもしれません。詳しくはおググりください。
