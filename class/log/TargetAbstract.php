<?php
namespace bot\log;

/**
 * ログ出力クラスの基本的な処理を実装する抽象クラス
 */
abstract class TargetAbstract implements TargetInterface {
    const LOG_LEVEL_TRACE   = 0;
    const LOG_LEVEL_DEBUG   = 1;
    const LOG_LEVEL_INFO    = 2;
    const LOG_LEVEL_SUCCESS = 3;
    const LOG_LEVEL_WARNING = 4;
    const LOG_LEVEL_ERROR   = 5;
    const LOG_LEVEL_MAX     = self::LOG_LEVEL_ERROR;

    /**
     * ログ出力を行う最少のログレベルを取得
     *
     * @return int  ログレベル
     */
    abstract public function getMinLogLevel();

    /**
     * ログ出力を行う最大のログレベルを取得
     *
     * よほど変なことを考えない限りは、LOG_LEVEL_MAX を指定しておけばよい
     *
     * @return int  ログレベル
     */
    public function getMaxLogLevel() {
        return self::LOG_LEVEL_MAX;
    }


    /**
     * ログ出力を行う関数
     *
     * @param   int     $time       イベント発生時間のタイムスタンプ
     * @param   string  $text       ログ内容
     * @param   string  $level      ログレベル
     * @param   int     $int_level  ログレベル（整数値に変換したもの）
     */
    abstract public function writeImpl($time, $text, $level, $int_level);

    /**
     * @inheritdoc
     */
    public function write($data, $level) {
        $now = time();
        $int_level = $this->convertStringErrorLevelToInt($level);
        $min_level = $this->getMinLogLevel();
        $max_level = $this->getMaxLogLevel();
        if($min_level <= $int_level && $int_level <= $max_level) {
            if($data === null) { // is_scalar は NULL をスカラ扱いしない
                $text = '(NULL)';
            } elseif($data === true) { // boolean をそのまま stringify すると "0"/"1" で誰も幸せにならない
                $text = '(TRUE)';
            } elseif($data === false) {
                $text = '(FALSE)';
            } elseif(is_resource($data)) { // is_scalarはリソース型にfalseを返すがこの挙動に依存するなと書いてあるので先に判定
                $text = '(RESOURCE)';
            } elseif(is_scalar($data)) { // integer, float, string
                $text = (string)$data;
            } elseif(is_object($data) && is_callable([$data, '__toString'])) {
                $text = $data->__toString();
            } else {
                $text = '';
                if(is_object($data)) {
                    $text = '<class:' . get_class($data) . '> ';
                }
                $json = @json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                $text .= ($json === false) ? '(ERROR)' : $json;
            }

            // 実装クラスが楽になるように複数行にまたがるものはあらかじめ各行に分割してから送る
            foreach(preg_split('/\x0d\x0a|\x0d|\x0a/', $text) as $line) {
                $this->writeImpl($now, $line, $level, $int_level);
            }
        }
    }

    /**
     * 文字列のログレベルを整数値に変換する関数
     *
     * @param   string  $level  ログレベル
     * @return  int     ログレベル（整数値に変換したもの）
     */
    protected function convertStringErrorLevelToInt($level) {
        switch(strtolower($level)) {
        case 'trace':   return self::LOG_LEVEL_TRACE;
        case 'debug':   return self::LOG_LEVEL_DEBUG;
        case 'info':    return self::LOG_LEVEL_INFO;
        case 'success': return self::LOG_LEVEL_SUCCESS;
        case 'warning': return self::LOG_LEVEL_WARNING;
        case 'error':   return self::LOG_LEVEL_ERROR;
        default:        return self::LOG_LEVEL_INFO;
        }
    }
}
