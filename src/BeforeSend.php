<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Event;

final class BeforeSend implements BeforeSendInterface
{
    /** @param array<string, string> $tags セットしたいタグの配列(オプション) */
    public function __construct(private readonly array $tags = [])
    {
    }

    public function __invoke(Event $event): Event
    {
        if ($this->tags !== []) {
            $event->setTags($this->tags);
        }

        return $event;
    }
}
