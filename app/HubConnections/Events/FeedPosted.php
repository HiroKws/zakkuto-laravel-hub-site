<?php

namespace App\HubConnections\Events;

use Carbon\Carbon;

class FeedPosted extends HubConnectionBaseEvent
{
    /** @var string * */
    public $author;

    /** @var string * */
    public $content;

    /** @var Carbon */
    public $date;

    /** @var string * */
    public $id;

    /** @var string * */
    public $image;

    /** @var string * */
    public $intro;

    /** @var string * */
    public $name;

    /** @var string * */
    public $source;

    /** @var string * */
    public $tags;

    public function __toString()
    {
        // 日付はタイムゾーンがバラバラなので、明示的に日本時間にする
        return 'URL：'.$this->source."\n"
            .'日付：'.$this->date
                ->timezone('Asia/Tokyo')
                ->toDateTimeString()."\n"
            .'コンテンツ：'."\n".$this->content."\n";
    }
}
