<?php
namespace bot\log;

/**
 * ログをファイル出力するクラス
 */
class File extends TargetAbstract {
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
    private $min_log_level = self::LOG_LEVEL_INFO;

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
    private $fh = null;

    /**
     * コンストラクタ
     *
     * ログファイル名を確定し、ファイルを開くところまで
     */
    public function __construct() {
        $log_file_path = __DIR__ . '/' . self::LOG_DIRECTORY . '/' . date('Y-m-d', time()) . '.txt';
        if(!file_exists(dirname($log_file_path))) {
            mkdir(dirname($log_file_path), 0755, true);
        }
        if(!$this->fh = fopen($log_file_path, 'c+t')) {
            throw new \Exception('Could not open log file: ' . $log_file_path);
        }
    }

    /**
     * デストラクタ
     *
     * まだ書き出されていないログがあれば書き出し、ファイルを閉じる
     */
    public function __destruct() {
        $this->flush();
        fclose($this->fh);
        $this->fh = null;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getMinLogLevel() {
        return $this->min_log_level;
    }

    /**
     * {@inheritdoc}
     */
    public function writeImpl($time, $text, $level, $int_level) {
        $this->buffer[] = sprintf(
            "[%s] [%s] %s",
            date('Y-m-d H:i:sO', $time),
            substr($level . '       ', 0, 7),
            $text
        );
        if(count($this->buffer) >= 10) {
            $this->flush();
        }
    }

    /**
     * バッファにたまったログをファイルに書き出す
     */
    private function flush() {
        if(!$this->fh || !$this->buffer) {
            return;
        }
        flock($this->fh, LOCK_EX);
        fseek($this->fh, 0, SEEK_END);
        fwrite($this->fh, implode("\n", $this->buffer) . "\n");
        fflush($this->fh);
        flock($this->fh, LOCK_UN);
        $this->buffer = [];
    }
}
