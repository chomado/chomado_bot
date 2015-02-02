<?php
/**
 * ログ出力クラスが実装しなければならないインタフェース
 */
interface Log_TargetInterface {
    /**
     * ログを出力する
     *
     * @var mixed   $data   ログ出力内容
     * @var string  $level  ログ出力レベル
     */
    public function write($data, $level);
}
