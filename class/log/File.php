<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\log;

/**
 * ログをファイル出力するクラス
 */
class File extends TargetAbstract
{
    /**
     * ログ保存ディレクトリ
     */
    const LOG_DIRECTORY = '../../runtime/logs';

    /**
     * 最小のログレベル
     *
     * @var int
     * @see getMinLogLevel()
     */
    private $minLogLevel = self::LOG_LEVEL_INFO;

    /**
     * ログをまとめて出力するためのバッファ
     *
     * @var array
     */
    private $buffer = [];

    /**
     * ログ出力ファイルのファイルハンドル
     *
     * このクラスが生成された時に対象ファイルは固定される。
     * （途中で日付が変わっても何事もなかったかのように処理される）
     */
    private $logHandle = null;

    /**
     * コンストラクタ
     *
     * ログファイル名を確定し、ファイルを開くところまで
     */
    public function __construct()
    {
        $logFilePath = __DIR__ . '/' . self::LOG_DIRECTORY . '/' . date('Y-m-d', time()) . '.txt';
        if (!file_exists(dirname($logFilePath))) {
            mkdir(dirname($logFilePath), 0755, true);
        }
        if (!$this->logHandle = fopen($logFilePath, 'c+t')) {
            throw new \Exception('Could not open log file: ' . $logFilePath);
        }
    }

    /**
     * デストラクタ
     *
     * まだ書き出されていないログがあれば書き出し、ファイルを閉じる
     */
    public function __destruct()
    {
        $this->flush();
        fclose($this->logHandle);
        $this->logHandle = null;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getMinLogLevel()
    {
        return $this->minLogLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function writeImpl($time, $text, $level, $intLevel)
    {
        $this->buffer[] = sprintf(
            "[%s] [%s] %s",
            date('Y-m-d H:i:sO', $time),
            substr($level . '       ', 0, 7),
            $text
        );
        if (count($this->buffer) >= 10) {
            $this->flush();
        }
    }

    /**
     * バッファにたまったログをファイルに書き出す
     */
    private function flush()
    {
        if (!$this->logHandle || empty($this->buffer)) {
            return;
        }
        flock($this->logHandle, LOCK_EX);
        fseek($this->logHandle, 0, SEEK_END);
        fwrite($this->logHandle, implode("\n", $this->buffer) . "\n");
        fflush($this->logHandle);
        flock($this->logHandle, LOCK_UN);
        $this->buffer = [];
    }
}
