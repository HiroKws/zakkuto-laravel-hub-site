<?php

namespace App\HubConnections\Listeners;

use App\HubConnections\Events\StringizableInterface;
use App\Services\JsonPoster;

class ChatSender
{
    /** @var JsonPoster */
    private $poster;

    public function __construct(JsonPoster $poster)
    {
        $this->poster = $poster;
    }

    public function handle(StringizableInterface $event)
    {
        // URLにキーが含まれているため機密情報扱い
        $url = env('SLACK_INCOMING_WEBHOOK_URL');

        $data = [
            // 明示的に型変換しないとベントクラスインスタンスがセットされる
            'text'       => (string) $event,
            'username'   => 'Hubサイト経由',
            'channel'    => '@hirokws',
            'icon_emoji' => ':heavy_check_mark:',
        ];

        $this->poster->post($url, $data);
    }
}
