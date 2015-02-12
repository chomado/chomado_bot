<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;

/**
 * ログ出力を行うクラス
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Log {
    /**
     * 実行トレース用のログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function trace($data) {
        self::log($data, 'trace');
    }

    /**
     * デバッグ用のログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function debug($data) {
        self::log($data, 'debug');
    }

    /**
     * 情報レベルのログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function info($data) {
        self::log($data, 'info');
    }

    /**
     * 成功した時のログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function success($data) {
        self::log($data, 'success');
    }

    /**
     * 警告レベルのログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function warning($data) {
        self::log($data, 'warning');
    }

    /**
     * エラーレベルのログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     */
    public static function error($data) {
        self::log($data, 'error');
    }

    /**
     * ログを出力する
     *
     * @param   mixed   $data   ログ出力内容
     * @param   string  $level  ログレベル
     * @see trace()
     * @see debug()
     * @see info()
     * @see warning()
     * @see error()
     */
    public static function log($data, $level) {
        self::getInstance()->write($data, $level);
    }

    /**
     * PHPが発生させるエラー・警告類をこの logger がハンドリングするように設定する
     */
    public static function setErrorHandler() {
        set_error_handler([self::getInstance(), 'errorHandlerCallback'], E_ALL | E_STRICT);
    }

// ==================================================================
// ここから内部関数・変数

    /**
     * このクラスの singleton インスタンスを保持する変数
     *
     * @var self
     * @see getInstance()
     */
    private static $instance;

    /**
     * 実際のログ出力を行うインスタンスの配列
     *
     * @var log\TargetInterface[]
     */
    private $targets = [];

    /**
     * このクラスの singleton インスタンスを生成して返す
     *
     * @return self
     */
    private static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     *
     * @see getInstance()
     *
     * @todo ログターゲットを設定から読むようにする
     */
    private function __construct() {
        $this->targets = [
            new log\Console(),
            new log\File(),
        ];
    }

    /**
     * ログ出力内容を実際に出力するクラスに引き渡す
     * 
     * @param $data   mixed   ログ出力内容
     * @param $level  string  ログレベル
     */
    private function write($data, $level) {
        foreach($this->targets as $target) {
            $target->write($data, $level);
        }
    }

    /**
     * PHPが発生させたエラー・警告等を受けとるためのハンドラ
     *
     * コード上は全てのエラーをハンドリングできるように書いてあるが、
     * E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING は
     * 実際にはこの関数でハンドリングすることはできない。
     * 詳しくは PHP のマニュアルを参照のこと。
     * http://php.net/manual/ja/function.set-error-handler.php
     *
     * @internal
     * @param   int     $errno      発生させるエラーのレベル
     * @param   string  $errstr     エラーメッセージ
     * @param   string  $errfile    エラーが発生したファイルの名前
     * @param   int     $errline    エラーが発生した行番号
     * @return  bool この関数が FALSE を返した場合は、通常のエラーハンドラが処理を引き継ぎます
     * 
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public static function errorHandlerCallback($errno, $errstr, $errfile, $errline) {
        // @で抑制されている場合等にも呼び出されるので、現在の設定と発生した内容を照らし合わせて
        // 該当しなければ何もしせずに戻す
        if(!(error_reporting() & $errno)) {
            return true;
        }

        list($level, $die, $level_string) = self::convertErrorNumber($errno);
        $output = sprintf(
            '[%s] %s at %s line %d',
            $level_string,
            $errstr,
            $errfile,
            $errline
        );
        self::log($output, $level);
        if($die) {
            exit(1);
        }
        return true;
    }

    /**
     * PHPの発生するエラー番号から情報を変換して取り扱えるようにする
     *
     * @internal
     * @param   int     $errno      PHPの発生させたエラー番号
     * @return  array               [ エラーレベル:string, 処理後スクリプトを終了するか:bool, エラー番号の文字列表現:string ]
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private static function convertErrorNumber($errno) {
        switch($errno) {
        case E_ERROR:                   return ['error',   true,  'E_ERROR'];
        case E_WARNING:                 return ['warning', false, 'E_WARNING'];
        case E_PARSE:                   return ['error',   true,  'E_PARSE'];
        case E_NOTICE:                  return ['info',    false, 'E_NOTICE'];
        case E_CORE_ERROR:              return ['error',   true,  'E_CORE_ERROR'];
        case E_CORE_WARNING:            return ['warning', false, 'E_CORE_WARNING'];
        case E_COMPILE_ERROR:           return ['error',   true,  'E_COMPILE_ERROR'];
        case E_COMPILE_WARNING:         return ['warning', false, 'E_COMPILE_WARNING'];
        case E_USER_ERROR:              return ['error',   true,  'E_USER_ERROR'];
        case E_USER_WARNING:            return ['warning', false, 'E_USER_WARNING'];
        case E_USER_NOTICE:             return ['info',    false, 'E_USER_NOTICE'];
        case E_STRICT:                  return ['info',    false, 'E_STRICT'];
        case E_RECOVERABLE_ERROR:       return ['error',   true,  'E_RECOVERABLE_ERROR'];
        case E_DEPRECATED:              return ['info',    false, 'E_DEPRECATED'];
        case E_USER_DEPRECATED:         return ['info',    false, 'E_USER_DEPRECATED'];
        }
        return ['info', false, "?{$errno}?"];
    }
}
