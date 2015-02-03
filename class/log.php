<?php
require_once(__DIR__ . '/log/console.php');
require_once(__DIR__ . '/log/file.php');

/**
 * ログ出力を行うクラス
 */
class Log {
    /**
     * 実行トレース用のログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function trace($data) {
        self::log($data, 'trace');
    }

    /**
     * デバッグ用のログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function debug($data) {
        self::log($data, 'debug');
    }

    /**
     * 情報レベルのログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function info($data) {
        self::log($data, 'info');
    }

    /**
     * 成功した時のログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function success($data) {
        self::log($data, 'success');
    }

    /**
     * 警告レベルのログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function warning($data) {
        self::log($data, 'warning');
    }

    /**
     * エラーレベルのログを出力する
     *
     * @var mixed   $data   ログ出力内容
     */
    public static function error($data) {
        self::log($data, 'error');
    }

    /**
     * ログを出力する
     *
     * @var mixed   $data   ログ出力内容
     * @var string  $level  ログレベル
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
     * @var object (self)
     * @see getInstance()
     */
    private static $instance;

    /**
     * 実際のログ出力を行うインスタンスの配列
     *
     * @var array (array<Log_TargetInterface>)
     */
    private $targets = [];

    /**
     * このクラスの singleton インスタンスを生成して返す
     *
     * @return object (self)
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
     */
    private function __construct() {
        //FIXME: 設定から読む
        $this->targets = [
            new Log_Console(),
            new Log_File(),
        ];
    }

    /**
     * ログ出力内容を実際に出力するクラスに引き渡す
     * 
     * @var $data   mixed   ログ出力内容
     * @var $level  string  ログレベル
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
     * @var int     $errno      発生させるエラーのレベル
     * @var string  $errstr     エラーメッセージ
     * @var string  $errfile    エラーが発生したファイルの名前
     * @var int     $errline    エラーが発生した行番号
     * @var array   $errcontext エラーが発生した場所のアクティブシンボルテーブルを指す配列
     * @return bool この関数が FALSE を返した場合は、通常のエラーハンドラが処理を引き継ぎます
     */
    public static function errorHandlerCallback($errno, $errstr, $errfile, $errline, $errcontext) {
        // @で抑制されている場合等にも呼び出されるので、現在の設定と発生した内容を照らし合わせて
        // 該当しなければ何もしせずに戻す
        if(!(error_reporting() & $errno)) {
            return true;
        }

        $level  = 'info';
        $die    = false;
        $level_string = '?' . $errno . '?';
        switch($errno) { // {{{
        case E_ERROR: //実際にはハンドルできない
            $level = 'error';
            $die = true;
            $level_string = 'E_ERROR';
            break;
        case E_WARNING:
            $level = 'warning';
            $die = false;
            $level_string = 'E_WARNING';
            break;
        case E_PARSE: //実際にはハンドルできない
            $level = 'error';
            $die = true;
            $level_string = 'E_PARSE';
            break;
        case E_NOTICE:
            $level = 'info';
            $die = false;
            $level_string = 'E_NOTICE';
            break;
        case E_CORE_ERROR: //実際にはハンドルできない
            $level = 'error';
            $die = true;
            $level_string = 'E_ERROR';
            break;
        case E_CORE_WARNING: //実際にはハンドルできない
            $level = 'warning';
            $die = false;
            $level_string = 'E_CORE_WARNING';
            break;
        case E_COMPILE_ERROR: //実際にはハンドルできない
            $level = 'error';
            $die = true;
            $level_string = 'E_COMPILE_ERROR';
            break;
        case E_COMPILE_WARNING: //実際にはハンドルできない
            $level = 'warning';
            $die = false;
            $level_string = 'E_COMPILE_WARNING';
            break;
        case E_USER_ERROR:
            $level = 'error';
            $die = true;
            $level_string = 'E_USER_ERROR';
            break;
        case E_USER_WARNING:
            $level = 'warning';
            $die = false;
            $level_string = 'E_USER_WARNING';
            break;
        case E_USER_NOTICE:
            $level = 'info';
            $die = false;
            $level_string = 'E_USER_NOTICE';
            break;
        case E_STRICT:
            $level = 'info';
            $die = false;
            $level_string = 'E_STRICT';
            break;
        case E_RECOVERABLE_ERROR:
            // ユーザー定義のハンドラでエラーがキャッチされなかった場合は、 E_ERROR として異常終了する
            // とマニュアルに書いてあるので終了することにする
            $level = 'error';
            $die = true;
            $level_string = 'E_RECOVERABLE_ERROR';
            break;
        case E_DEPRECATED:
            $level = 'info';
            $die = false;
            $level_string = 'E_DEPRECATED';
            break;
        case E_USER_DEPRECATED:
            $level = 'info';
            $die = false;
            $level_string = 'E_USER_DEPRECATED';
            break;
        } // }}}
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
}
