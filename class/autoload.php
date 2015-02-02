<?php
require_once(__DIR__ . '/log.php'); // Autoloadされる前にロガーが動かないと悲しいので明示的にロードする

/**
 * クラスの自動ロードを設定・処理するクラス
 */
class Autoload {
    /**
     * 自動ロードを設定する
     *
     * この関数によって設定されるオートローダは次のルール:
     *      class HogeFuga が必要
     *          → .../hogefuga.php
     *      class HogeFuga_Piyo が必要
     *          → .../hogefuga/piyo.php
     */
    public static function register() {
        spl_autoload_register(function($class) {
            $path = __DIR__ . '/' . implode('/', explode('_', strtolower($class))) . '.php';
            if(file_exists($path)) {
                Log::trace("Autoloader: load " . $path);
                require_once($path);
                if(!class_exists($class, false)) {
                    Log::warning("Autoloader worked for '{$class}', but still not defined.");
                } else {
                    Log::trace("Autoloader: class '{$class}' loaded.");
                }
            } else {
                Log::warning("Autoloader: file not exist: $path");
            }
        });
    }
}
