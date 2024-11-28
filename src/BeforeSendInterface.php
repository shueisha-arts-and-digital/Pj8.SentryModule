<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Event;

interface BeforeSendInterface
{
    public function __invoke(Event $event): Event|null;
}
