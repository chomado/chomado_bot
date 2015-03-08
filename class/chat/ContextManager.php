<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\chat;

/**
 * docomo対話APIによるチャット時のコンテキストを保持するためのクラス
 */
class ContextManager
{
    /**
     * コンテキスト保持のためのデータファイルパス
     *
     * このファイル(__FILE__)からの相対パスで指定。
     * 現在はJSONだがこの中身についてこのクラスの他は知る必要は無い
     *
     * @see getDataFilePath()
     */
    const DATA_FILE_PATH = '../../runtime/chat_context.json';

    /**
     * コンテキストの有効時間(秒)
     *
     * 前回の使用からこの秒数が経過した場合、そのコンテキストデータは
     * 無効化される。本来はdocomo側の仕様以下になるようにするのが良いが、
     * 特に見つけられなかった。
     *
     * @see cleanupOldData()
     */
    const CONTEXT_EXPIRES = 1800;

    /**
     * データファイル操作時のファイルハンドル
     *
     * ファイルハンドルの寿命はこのクラスのインスタンスと同じになる。
     * load(), save() でそれぞれ開いて処理する場合、
     * 仮に複数のプログラムが同時に動く場合にデータが一部消失する可能性がある。
     * ファイルロックを併用することで可能な限り避けようという考え。
     *
     * @var resource
     * @see __construct()
     * @see __destruct()
     * @see load()
     * @see save()
     */
    private $fileHandle;

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
     * new された時点で全てのデータを読み込むので、
     * 複数のツイートを一括で処理する場合は一度だけ new して使い回すことを推奨
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * デストラクタ
     *
     * 保持しているデータは自動的に保存される
     */
    public function __destruct()
    {
        $this->cleanupOldData();
        $this->save();
    }

    /**
     * docomoAPIから指定されたコンテキストを保存する
     *
     * @param string $userId    ユーザを識別するための記号。screen_name や id_str
     * @param string $contextId docomoAPIから指定されたコンテキストID
     * @param string $mode      docomoAPIから指定された会話モード。 "dialog" or "srtr"
     */
    public function setContext($userId, $contextId, $mode)
    {
        if ($contextId != '') {
            $this->data[$userId] = [
                'context'   => $contextId,
                'mode'      => $mode,
                'expires'   => time() + self::CONTEXT_EXPIRES,
            ];
        } else {
            // コンテキストIDが空なら削除する
            unset($this->data[$userId]);
        }
    }

    /**
     * docomoAPIに指定するコンテキストIDを取得する
     *
     * @param string $userId ユーザを識別するための記号。screen_name や id_str
     * @return string コンテキストID。保存されていないか期限切れなら null
     * @see getContext()
     */
    public function getContextId($userId)
    {
        $context = $this->getContext($userId);
        return $context && isset($context['context']) ? $context['context'] : null;
    }

    /**
     * docomoAPIに指定する会話モードを取得する
     *
     * @param string $userId ユーザを識別するための記号。screen_name や id_str
     * @return string 会話モード。保存されていないか期限切れなら null
     * @see getContext()
     */
    public function getMode($userId)
    {
        $context = $this->getContext($userId);
        return $context && isset($context['mode']) ? $context['mode'] : null;
    }

    /**
     * docomoAPIに指定するコンテキストを取得する
     *
     * @param string $userId ユーザを識別するための記号。screen_name や id_str
     * @return array コンテキストデータ。保存されていないか期限切れなら null
     * @see setContext()
     * @see getContextId()
     * @see getMode()
     */
    public function getContext($userId)
    {
        // 保存されており、期限が切れていない
        if (isset($this->data[$userId]) && $this->data[$userId]['expires'] > time()) {
            // 一度使用されたら期限を伸ばす
            $this->data[$userId]['expires'] = time() + self::CONTEXT_EXPIRES;
            return [
                'context'   => $this->data[$userId]['context'],
                'mode'      => $this->data[$userId]['mode'],
            ];
        }
        return null;
    }

    /**
     * 期限のきれたデータを削除する。通常明示的に呼ぶ必要はない。
     */
    public function cleanupOldData()
    {
        if (empty($this->data)) {
            return;
        }
        foreach (array_keys($this->data) as $userId) {
            if ($this->data[$userId]['expires'] <= time()) {
                unset($this->data[$userId]);
            }
        }
    }

    /**
     * データファイルに保存されたデータを全て読み込む
     */
    private function load()
    {
        if ($this->fileHandle) {
// どうやら既に読まれている
            return;
        }
        $path = $this->getDataFilePath();
        $this->fileHandle = @fopen($path, 'c+');
        if (!$this->fileHandle) {
            throw new \Exception('Could not open data file: ' . $path);
        }
        flock($this->fileHandle, LOCK_EX);
        fseek($this->fileHandle, 0, SEEK_SET);
        $json = stream_get_contents($this->fileHandle);
        $data = @json_decode($json, true);
        $this->data = is_array($data) ? $data : [];
    }

    /**
     * メモリに保持しているデータをファイルに保存する
     */
    private function save()
    {
        if (!$this->fileHandle || !is_array($this->data)) {
            return;
        }
        fseek($this->fileHandle, 0, SEEK_SET);
        fwrite($this->fileHandle, json_encode($this->data, JSON_PRETTY_PRINT));
        ftruncate($this->fileHandle, ftell($this->fileHandle));
        fflush($this->fileHandle);
        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
        $this->fileHandle = null;
    }

    /**
     * データファイルのフルパスを取得する
     */
    private function getDataFilePath()
    {
        return __DIR__ . '/' . self::DATA_FILE_PATH;
    }
}
