<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\log;

/**
 * ログをコンソール出力するクラス
 */
class Console extends TargetAbstract
{
    /**
     * 最小のログレベル
     *
     * @var int
     * @see getMinLogLevel()
     *
     * @todo 設定でデバッグモードか切り替えられるように
     */
    private $minLogLevel = self::LOG_LEVEL_DEBUG;

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
        printf(
            "[%s] [%s] %s\n",
            date('Y-m-d H:i:sO', $time),
            $this->decorate(substr($level . '       ', 0, 7), $intLevel),
            $this->decorate($text, $intLevel)
        );
    }

    /**
     * エスケープシーケンス用カラーテーブル
     */
    private static $foregroundColors = [
        'black'         => '0;30',
        'blue'          => '0;34',
        'green'         => '0;32',
        'cyan'          => '0;36',
        'red'           => '0;31',
        'purple'        => '0;35',
        'brown'         => '0;33',
        'light_gray'    => '0;37',
        'dark_gray'     => '1;30',
        'light_blue'    => '1;34',
        'light_green'   => '1;32',
        'light_cyan'    => '1;36',
        'light_red'     => '1;31',
        'light_purple'  => '1;35',
        'yellow'        => '1;33',
        'white'         => '1;37',
    ];

    /**
     * エスケープシーケンス用カラーテーブル
     */
    private static $backgroundColors = [
        'black'        => '40',
        'red'          => '41',
        'green'        => '42',
        'yellow'       => '43',
        'blue'         => '44',
        'magenta'      => '45',
        'cyan'         => '46',
        'light_gray'   => '47',
    ];

    /**
     * エラーレベルから表示色にマップするテーブル
     *
     * ```
     *  [
     *      level => [ foreground-color-name, background-color-name ],
     *  ]
     * ```
     */
    private static $errorLevelColorMap = [
        self::LOG_LEVEL_TRACE   => [ 'dark_gray',   null ],
        self::LOG_LEVEL_DEBUG   => [ 'light_gray',  null ],
        self::LOG_LEVEL_INFO    => [ 'white',       null ],
        self::LOG_LEVEL_SUCCESS => [ 'light_green', null ],
        self::LOG_LEVEL_WARNING => [ 'yellow',      null ],
        self::LOG_LEVEL_ERROR   => [ 'light_red',   null ],
    ];

    /**
     * ログレベルに合わせてテキストを装飾する
     *
     * @param   string  $text       対象テキスト
     * @param   int     $intLevel  ログレベル定数
     * @return  string  装飾されたテキスト
     */
    private function decorate($text, $intLevel)
    {
        $colors = isset(self::$errorLevelColorMap[$intLevel]) ? self::$errorLevelColorMap[$intLevel] : null;
        if (!$colors) {
            return $text;
        }

        $ret = '';
        // 文字色
        if (isset(self::$foregroundColors[$colors[0]])) {
            $ret .= "\033[" . self::$foregroundColors[$colors[0]] . "m";
        }
        // 背景色
        if (isset(self::$backgroundColors[$colors[1]])) {
            $ret .= "\033[" . self::$backgroundColors[$colors[1]] . "m";
        }
        $ret .= $text;
        // 色を戻す
        $ret .= "\033[0m";
        return $ret;
    }
}
