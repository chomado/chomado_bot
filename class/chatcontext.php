<?php
class ChatContext {
    const DATA_FILE_PATH = '../tweet_content_data_list/chat_context.json';
    const CONTEXT_EXPIRES = 1800; // [秒] この秒数が経過するとコンテキストを忘れる

    private $fh;
    private $data;

    public function __construct() {
        $this->load();
    }

    public function __destruct() {
        $this->gc();
        $this->save();
    }

    public function setContextId($user_id, $context) {
        if($context != '') {
            $this->data[$user_id] = [
                'context' => $context,
                'expires' => time() + self::CONTEXT_EXPIRES,
            ];
        } else {
            unset($this->data[$user_id]);
        }
    }

    public function getContextId($user_id) {
        if(isset($this->data[$user_id]) && $this->data[$user_id]['expires'] > time()) {
            $this->data[$user_id]['expires'] = time() + self::CONTEXT_EXPIRES;
            return $this->data[$user_id]['context'];
        }
        return null;
    }

    public function gc() {
        if(!$this->data) {
            return;
        }
        foreach(array_keys($this->data) as $user_id) {
            if($this->data[$user_id]['expires'] <= time()) {
                unset($this->data[$user_id]);
            }
        }
    }

    private function load() {
        if($this->fh) {
            return;
        }
        $path = $this->getDataFilePath();
        if(!@file_exists($path)) {
            @touch($path);
        }
        if(!$this->fh = @fopen($path, 'r+')) {
            throw new Exception('Could not open data file');
        }
        flock($this->fh, LOCK_EX);
        fseek($this->fh, 0, SEEK_SET);
        $json = stream_get_contents($this->fh);
        $data = @json_decode($json, true);
        $this->data = is_array($data) ? $data : [];
    }

    private function save() {
        if(!$this->fh || !is_array($this->data)) {
            return;
        }
        fseek($this->fh, 0, SEEK_SET);
        fwrite($this->fh, json_encode($this->data, JSON_PRETTY_PRINT));
        ftruncate($this->fh, ftell($this->fh));
        flock($this->fh, LOCK_UN);
        fclose($this->fh);
    }

    private function getDataFilePath() {
        return __DIR__ . '/' . self::DATA_FILE_PATH;
    }
}
