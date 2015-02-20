<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\chat;

/**
 * docomo対話APIによるチャット時のコンテキストを保持するためのクラス
 */
class ContextManager {
    /**
     * コンテキスト保持のためのデータファイルパス
     *
     * このファイル(__FILE__)からの相対パスで指定。
     * 現在はJSONだがこの中身についてこのクラスの他は知る必要は無いし触ってはいけない
     * 
     * @see getDataFilePath()
     */
    const DATA_FILE_PATH = '../../runtime/chat_context.json';

    /**
     * コンテキストの有効時間(秒)
     *
     * 前回の使用からこの秒数が経過した場合、そのコンテキストデータは無効化される。
     * 本来はdocomo側の仕様以下になるようにするのが良いが、特に見つけられなかった。
     * 
     * @see cleanupOldData()
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
    private $file_handle;

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
        $this->cleanupOldData();
        $this->save();
    }

    /**
     * docomoAPIから指定されたコンテキストを保存する
     * 
     * @param string $user_id       ユーザを識別するための記号。典型的には user->screen_name や user->id_str
     * @param string $context_id    docomoAPIから指定されたコンテキストID
     * @param string $mode          docomoAPIから指定された会話モード。 "dialog" or "srtr"
     */
    public function setContext($user_id, $context_id, $mode) {
        if($context_id != '') {
            $this->data[$user_id] = [
                'context'   => $context_id,
                'mode'      => $mode,
                'expires'   => time() + self::CONTEXT_EXPIRES,
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
     * @see getContext()
     */
    public function getContextId($user_id) {
        $context = $this->getContext($user_id);
        return $context && isset($context['context']) ? $context['context'] : null;
    }

    /**
     * docomoAPIに指定する会話モードを取得する
     * 
     * @param string $user_id ユーザを識別するための記号。典型的には user->screen_name や user->id_str
     * @return string 会話モード。保存されていないか期限切れなら null
     * @see getContext()
     */
    public function getMode($user_id) {
        $context = $this->getContext($user_id);
        return $context && isset($context['mode']) ? $context['mode'] : null;
    }

    /**
     * docomoAPIに指定するコンテキストを取得する
     * 
     * @param string $user_id ユーザを識別するための記号。典型的には user->screen_name や user->id_str
     * @return array コンテキストデータ。保存されていないか期限切れなら null
     * @see setContext()
     * @see getContextId()
     * @see getMode()
     */
    public function getContext($user_id) {
        if(isset($this->data[$user_id]) &&              // 保存されている
           $this->data[$user_id]['expires'] > time())   // 期限が切れていない
        {
            $this->data[$user_id]['expires'] = time() + self::CONTEXT_EXPIRES; // 一度使用されたら期限を伸ばす（処理としては美しくない）
            return [
                'context'   => $this->data[$user_id]['context'],
                'mode'      => $this->data[$user_id]['mode'],
            ];
        }
        return null;
    }

    /**
     * 期限のきれたデータを削除する。通常明示的に呼ぶ必要はない。
     */
    public function cleanupOldData() {
        if(empty($this->data)) {
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
        if($this->file_handle) { // どうやら既に読まれている
            return;
        }
        $path = $this->getDataFilePath();
        $this->file_handle = @fopen($path, 'c+');
        if(!$this->file_handle) {
            throw new \Exception('Could not open data file: ' . $path);
        }
        flock($this->file_handle, LOCK_EX);
        fseek($this->file_handle, 0, SEEK_SET);
        $json = stream_get_contents($this->file_handle);
        $data = @json_decode($json, true);
        $this->data = is_array($data) ? $data : [];
    }

    /**
     * メモリに保持しているデータをファイルに保存する
     */
    private function save() {
        if(!$this->file_handle || !is_array($this->data)) {
            return;
        }
        fseek($this->file_handle, 0, SEEK_SET);                                     // ファイルポインタを頭に戻す
        fwrite($this->file_handle, json_encode($this->data, JSON_PRETTY_PRINT));    // JSONデータを作成して保存する
        ftruncate($this->file_handle, ftell($this->file_handle));                   // ファイルサイズを正しいサイズにする
        fflush($this->file_handle);
        flock($this->file_handle, LOCK_UN);                                         // flockを解除する(PHP5.3.2以降、fcloseで解除されないので必須)
        fclose($this->file_handle);
        $this->file_handle = null;
    }

    /**
     * データファイルのフルパスを取得する
     */
    private function getDataFilePath() {
        return __DIR__ . '/' . self::DATA_FILE_PATH;
    }
}
