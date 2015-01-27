<?php
/**
 * docomo対話APIによるチャット時のコンテキストを保持するためのクラス
 */
class ChatContext {
    /**
     * コンテキスト保持のためのデータファイルパス
     *
     * このファイル(__FILE__)からの相対パスで指定。
     * 現在はJSONだがこの中身についてこのクラスの他は知る必要は無いし触ってはいけない
     * 
     * @see getDataFilePath()
     */
    const DATA_FILE_PATH = '../tweet_content_data_list/chat_context.json';

    /**
     * コンテキストの有効時間(秒)
     *
     * 前回の使用からこの秒数が経過した場合、そのコンテキストデータは無効化される。
     * 本来はdocomo側の仕様以下になるようにするのが良いが、特に見つけられなかった。
     * 
     * @see gc()
     */
    const CONTEXT_EXPIRES = 1800;

    /**
     * データファイル操作時のファイルハンドル
     *
     * ファイルハンドルの寿命はこのクラスのインスタンスと同じになる。
     * load(), save() でそれぞれ開いて処理する場合、仮に複数のプログラムが同時に動く場合に
     * データが一部消失する可能性がある。
     * ファイルロックを併用することで可能な限り避けようという考え。
     * 
     * @var resource
     * @see __construct()
     * @see __destruct()
     * @see load()
     * @see save()
     */
    private $fh;

    /**
     * データファイルから読み込んだデータ and/or 追加/削除したデータを保持する変数
     * 
     * ```
     *  [
     *      'user_id' => [
     *          'context' => 'foo',
     *          'expires' => 123456789,
     *      ],
     *      'user_id2' => [ ... ],
     *  ]
     * ```
     *
     * @var array
     * @see setContextId()
     * @see getContextId()
     */
    private $data;

    /**
     * コンストラクタ
     *
     * new された時点で全てのデータを読み込むので、複数のツイートを一括で処理する場合は
     * 一度だけ new して使い回すことを推奨
     */
    public function __construct() {
        $this->load();
    }

    /**
     * デストラクタ
     *
     * 保持しているデータは自動的に保存される
     */
    public function __destruct() {
        $this->gc();
        $this->save();
    }

    /**
     * docomoAPIから指定されたコンテキストIDを保存する
     * 
     * @param string $user_id ユーザを識別するための記号。典型的には user->screen_name や user->id_str
     * @param string $context docomoAPIから指定されたコンテキストID
     */
    public function setContextId($user_id, $context) {
        if($context != '') {
            $this->data[$user_id] = [
                'context' => $context,
                'expires' => time() + self::CONTEXT_EXPIRES,
            ];
        } else {
            // コンテキストIDが空なら削除する
            unset($this->data[$user_id]);
        }
    }

    /**
     * docomoAPIに指定するコンテキストIDを取得する
     * 
     * @param string $user_id ユーザを識別するための記号。典型的には user->screen_name や user->id_str
     * @return string コンテキストID。保存されていないか期限切れなら null
     */
    public function getContextId($user_id) {
        if(isset($this->data[$user_id]) &&              // 保存されている
           $this->data[$user_id]['expires'] > time())   // 期限が切れていない
        {
            $this->data[$user_id]['expires'] = time() + self::CONTEXT_EXPIRES; // 一度使用されたら期限を伸ばす（処理としては美しくない）
            return $this->data[$user_id]['context'];
        }
        return null;
    }

    /**
     * 期限のきれたデータを削除する。通常明示的に呼ぶ必要はない。
     */
    public function gc() {
        if(!$this->data) {
            return;
        }
        foreach(array_keys($this->data) as $user_id) {
            if($this->data[$user_id]['expires'] <= time()) {
                unset($this->data[$user_id]);
            }
        }
    }

    /**
     * データファイルに保存されたデータを全て読み込む
     */
    private function load() {
        if($this->fh) { // どうやら既に読まれている
            return;
        }
        $path = $this->getDataFilePath();
        if(!@file_exists($path)) {
            @touch($path);
        }
        if(!$this->fh = @fopen($path, 'r+')) {
            throw new Exception('Could not open data file');
        }
        flock($this->fh, LOCK_EX);
        fseek($this->fh, 0, SEEK_SET);
        $json = stream_get_contents($this->fh);
        $data = @json_decode($json, true);
        $this->data = is_array($data) ? $data : [];
    }

    /**
     * メモリに保持しているデータをファイルに保存する
     */
    private function save() {
        if(!$this->fh || !is_array($this->data)) {
            return;
        }
        fseek($this->fh, 0, SEEK_SET);                                  // ファイルポインタを頭に戻す
        fwrite($this->fh, json_encode($this->data, JSON_PRETTY_PRINT)); // JSONデータを作成して保存する
        ftruncate($this->fh, ftell($this->fh));                         // ファイルサイズを正しいサイズにする
        flock($this->fh, LOCK_UN);                                      // flockを解除する(PHP5.3.2以降、fcloseで解除されないので必須)
        fclose($this->fh);
        $this->fh = null;
    }

    /**
     * データファイルのフルパスを取得する
     */
    private function getDataFilePath() {
        return __DIR__ . '/' . self::DATA_FILE_PATH;
    }
}
