<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;

/**
 * 口から無限に文が出てくるクラス
 */
class RandomSentenceList implements \Countable, \Iterator
{
    /** @var int イテレータインタフェースのkey()で返すためだけのindex値 */
    private $index = 0;

    /** @var string[] 文のリスト */
    private $sentences = [];

    /**
     * コンストラクタ
     *
     * @param   string  $filePath   1行1形式のセンテンスリストを格納したファイルへのパス
     */
    public function __construct($filePath)
    {
        $this->loadFile($filePath);
    }

    /**
     * 出てくる可能性がある文の数を取得
     *
     * - count($instance) で取得できる
     * - 無限に取り出せるのでこの count() にそれほど意味は無い
     *
     * @return int
     */
    public function count()
    {
        return count($this->sentences);
    }

    /**
     * (iterator) 「今」のセンテンスを取得
     *
     * @return string
     */
    public function current()
    {
        return $this->get();
    }

    /**
     * (iterator) 「今」のキー（インデックス）を取得
     *
     * rewind() されてから何回取得されたかを取得できるが何の意味も無い
     *
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * (iterator) カーソルを進める
     *
     * @return void
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * (iterator) カーソルを先頭に巻き戻す
     *
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * (iterator) 現在のカーソル位置が有効な場所を指しているか返す
     *
     * このクラスは無限に値を返し続けるので、文リストに1つでもあれば常にtrue、
     * 空の文リストであれば常にfalseを返す
     *
     * @return bool
     */
    public function valid()
    {
        return !empty($this->sentences);
    }

    /**
     * ランダムに選択された文を取得
     *
     * @return string
     * @throws \Exception 空の文リストを保持していれば例外を投げる
     */
    public function get()
    {
        if (empty($this->sentences)) {
            throw new \Exception('There is no sentences');
        }
        $random = mt_rand(0, $this->count() - 1);
        return $this->sentences[$random];
    }

    /**
     * 指定されたファイルパスから1行1形式のセンテンスリストを読み込む
     *
     * @param   string  $filePath   1行1形式のセンテンスリストを格納したファイルへのパス
     * @return  void
     * @throws  \Exception  ファイルが開けない時、例外を投げる
     */
    private function loadFile($filePath)
    {
        $this->sentences = [];
        $this->index = 0;
        if (!$handle = @fopen($filePath, 'r')) {
            throw new \Exception('Could not open sentence list file');
        }
        while (!feof($handle)) {
            $line = trim(fgets($handle));
            if ($line !== '' && $line[0] !== '#') {
                $this->sentences[] = $line;
            }
        }
        fclose($handle);
    }
}
