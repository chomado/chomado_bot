<?php
namespace bot\log;

/**
 * ログ出力クラスが実装しなければならないインタフェース
 */
interface TargetInterface {
    /**
     * ログを出力する
     *
     * @var mixed   $data   ログ出力内容
     * @var string  $level  ログ出力レベル
     */
    public function write($data, $level);
}
