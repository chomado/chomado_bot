<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\log;

/**
 * ログ出力クラスが実装しなければならないインタフェース
 */
interface TargetInterface
{
    /**
     * ログを出力する
     *
     * @var mixed   $data   ログ出力内容
     * @var string  $level  ログ出力レベル
     */
    public function write($data, $level);
}
