<?php
use bot\RandomSentenceList;

/*
   public function current() {
   public function key() {
   public function next() {
   public function rewind() {
   public function valid() {
   public function get() {

*/

class RandomSentenceListTest extends PHPUnit_Framework_TestCase {
    private static $testListPath;
    private $target;

    public static function setUpBeforeClass() {
        self::$testListPath = tempnam(sys_get_temp_dir(), 'phpunit-');
        file_put_contents(
            self::$testListPath,
            implode("\n", [
                'ABC',          // <- 有効
                '# comment',    // <- コメント扱いで無視
                '123',          // <- 有効
                '',             // <- 空行は無視
                'あいう',       // <- 有効
            ])
        );
    }

    public static function tearDownAfterClass() {
        if(self::$testListPath != '' && file_exists(self::$testListPath)) {
            unlink(self::$testListPath);
        }
    }

    public function setUp() {
        $this->target = new RandomSentenceList(self::$testListPath);
    }

    public function testLoadAndCount() {
        $this->assertEquals(3, $this->target->count());
        $this->assertEquals($this->target->count(), count($this->target));
    }

    public function testNotExists() {
        $this->setExpectedException('Exception');
        $target = new RandomSentenceList(sys_get_temp_dir() . '/not-exists-' . hash('sha256', uniqid()));
    }

    public function testIteratorInterface() {
        $this->assertInstanceOf('Iterator', $this->target);
        $i = 0;
        $counts = [
            'ABC' => 0,
            '123' => 0,
            'あいう' => 0,
        ];
        foreach($this->target as $j => $text) {
            $this->assertEquals($i, $j); // next() が特に意味も無くインクリメントしているはずなので $i と key() は等しくなる
            $this->assertRegExp('/^ABC|123|あいう$/u', $text);
            ++$counts[$text];
            ++$i;
            // 放っておくと無限に続くので打ち切る
            if($i >= 50) {
                break;
            }
        }
        // このくらいは全部出てるんじゃないかなあという
        $this->assertGreaterThanOrEqual(5, $counts['ABC']);
        $this->assertGreaterThanOrEqual(5, $counts['123']);
        $this->assertGreaterThanOrEqual(5, $counts['あいう']);
    }

    public function testIteratorRewind() {
        $this->assertEquals(0, $this->target->key());
        $this->target->next();
        $this->assertEquals(1, $this->target->key());
        $this->target->rewind();
        $this->assertEquals(0, $this->target->key());
    }

    public function testIteratorEmptyValid() {
        $path = tempnam(sys_get_temp_dir(), 'phpunit-');
        touch($path);
        $target = new RandomSentenceList($path);
        $this->assertFalse($target->valid());
    }

    public function testGet() {
        $counts = [
            'ABC' => 0,
            '123' => 0,
            'あいう' => 0,
        ];
        for($i = 0; $i < 20; ++$i) {
            $text = $this->target->get();
            $this->assertRegExp('/^ABC|123|あいう$/u', $text);
            ++$counts[$text];
        }
        // このくらいは全部出てるんじゃないかなあという
        $this->assertGreaterThanOrEqual(3, $counts['ABC']);
        $this->assertGreaterThanOrEqual(3, $counts['123']);
        $this->assertGreaterThanOrEqual(3, $counts['あいう']);
    }

    public function testGetEmpty() {
        $this->setExpectedException('Exception');
        $path = tempnam(sys_get_temp_dir(), 'phpunit-');
        touch($path);
        $target = new RandomSentenceList($path);
        $target->get();
    }
}
