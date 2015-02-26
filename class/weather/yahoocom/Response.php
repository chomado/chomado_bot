<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\weather\yahoocom;

use stdClass;
use Exception;
use bot\DateTime;

/**
 * Yahoo!から取得した天気情報を表すクラス
 */
class Response
{
    /** @var \stdClass データがあらわされている単位系 */
    private $units;

    /** @var \stdClass 天気情報 */
    private $info;

    /**
     * コンストラクタ
     *
     * @param string $json Yahoo!から取得したJSON
     * @throws \Exception データが壊れているとき
     */
    public function __construct($json)
    {
        $decoded = @json_decode($json);
        if (!($decoded instanceof stdClass) || !isset($decoded->query->results->channel)) {
            throw new Exception('Broken json was given');
        }
        $channel = $decoded->query->results->channel;
        $this->units = $channel->units;
        $this->info = $channel->item;
    }

    /**
     * 「現在」の天気を取得
     *
     * @return \stdClass
     */
    public function getCondition()
    {
        $item = $this->info;
        return (object)[
            'weather'   => new model\Weather($item->condition->code, $item->condition->text),
            'temp'      => new model\Temperature($item->condition->temp, $this->units->temperature),
            'updatedAt' => new DateTime($item->condition->date, new \DateTimeZone('America/Los_Angeles')),
        ];
    }

    /**
     * 明日の天気を取得
     *
     * @return \stdClass
     */
    public function getTomorrow()
    {
        $tomorrow = $this->info->forecast[1];
        return (object)[
            'weather'   => new model\Weather($tomorrow->code, $tomorrow->text),
            'tempHigh'  => new model\Temperature($tomorrow->high, $this->units->temperature),
            'tempLow'   => new model\Temperature($tomorrow->low, $this->units->temperature),
        ];
    }

    /**
     * データ更新日時を取得
     *
     * @return \bot\DateTime
     */
    public function getUpdatedAt()
    {
        return new DateTime($this->info->pubDate, new \DateTimeZone('America/Los_Angeles'));
    }
}
