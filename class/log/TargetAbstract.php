<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\log;

/**
 * ログ出力クラスの基本的な処理を実装する抽象クラス
 */
abstract class TargetAbstract implements TargetInterface
{
    /** 分岐などを追うためのログレベル */
    const LOG_LEVEL_TRACE   = 0;

    /** デバッグ用の情報を表すログレベル */
    const LOG_LEVEL_DEBUG   = 1;

    /** エラーなどではないがそれなりに重要な情報を表すログレベル */
    const LOG_LEVEL_INFO    = 2;

    /** 操作に成功したことを表すログレベル */
    const LOG_LEVEL_SUCCESS = 3;

    /** 直ちに動作に問題が出るわけではないが警告されるべきことを表すログレベル */
    const LOG_LEVEL_WARNING = 4;

    /** 問題が発生したことを表すログレベル */
    const LOG_LEVEL_ERROR   = 5;

    /** LOG_LEVEL_* の最大値 */
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
    public function getMaxLogLevel()
    {
        return self::LOG_LEVEL_MAX;
    }


    /**
     * ログ出力を行う関数
     *
     * @param   int     $time       イベント発生時間のタイムスタンプ
     * @param   string  $text       ログ内容
     * @param   string  $level      ログレベル
     * @param   int     $intLevel   ログレベル（整数値に変換したもの）
     */
    abstract public function writeImpl($time, $text, $level, $intLevel);

    /**
     * {@inheritdoc}
     */
    public function write($data, $level)
    {
        $now = time();
        $intLevel = $this->convertStringErrorLevelToInt($level);
        $minLevel = $this->getMinLogLevel();
        $maxLevel = $this->getMaxLogLevel();
        if ($intLevel < $minLevel || $intLevel > $maxLevel) {
            return;
        }
        $text = $this->stringifyObject($data);

        // 実装クラスが楽になるように、
        // 複数行にまたがるものはあらかじめ各行に分割してから送る
        foreach (preg_split('/\x0d\x0a|\x0d|\x0a/', $text) as $line) {
            $this->writeImpl($now, $line, $level, $intLevel);
        }
    }

    /**
     * 文字列のログレベルを整数値に変換する関数
     *
     * @param   string  $level  ログレベル
     * @return  int     ログレベル（整数値に変換したもの）
     */
    protected function convertStringErrorLevelToInt($level)
    {
        switch(strtolower($level)) {
            case 'trace':
                return self::LOG_LEVEL_TRACE;
            case 'debug':
                return self::LOG_LEVEL_DEBUG;
            case 'info':
                return self::LOG_LEVEL_INFO;
            case 'success':
                return self::LOG_LEVEL_SUCCESS;
            case 'warning':
                return self::LOG_LEVEL_WARNING;
            case 'error':
                return self::LOG_LEVEL_ERROR;
            default:
                return self::LOG_LEVEL_INFO;
        }
    }

    /**
     * ログ出力を指示されたオブジェクトを文字列に変換する
     *
     * @param   mixed   $data   ログ出力を指示されたオブジェクト
     * @return                  ログ出力用に整形された文字列
     */
    protected function stringifyObject($data)
    {
        if ($data === null) {
// is_scalar は NULL をスカラ扱いしない
            return '(NULL)';
        } elseif (is_bool($data)) {
// boolean をそのまま stringify すると "0"/"1" で誰も幸せにならない
            return $data ? '(TRUE)' : '(FALSE)';
        } elseif (is_resource($data)) {
// is_scalarはリソース型にfalseを返すがこの挙動に依存するなと書いてあるので先に判定
            return '<resource:' . get_resource_type($data) . '>';
        } elseif (is_scalar($data)) {
// integer, float, string
            return (string)$data;
        } elseif (is_object($data) && is_callable([$data, '__toString'])) {
            return '<class:' . get_class($data) . '> ' . $data->__toString();
        }
        $text = '';
        if (is_object($data)) {
            $text = '<class:' . get_class($data) . '> ';
        }
        return $text . json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
