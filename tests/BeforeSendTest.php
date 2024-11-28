<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Event;

class BeforeSendTest extends TestCase
{
    public function testInvokeSetServiceNameTag(): void
    {
        $tags = ['service' => 'test-service'];
        $beforeSend = new BeforeSend($tags);
        $event = Event::createEvent();

        $modifiedEvent = $beforeSend($event);

        $this->assertSame('test-service', $modifiedEvent->getTags()['service']);
    }

    public function testInvokeNotSetServiceNameTag(): void
    {
        $beforeSend = new BeforeSend();
        $event = Event::createEvent();

        $modifiedEvent = $beforeSend($event);

        $this->assertEmpty($modifiedEvent->getTags());
    }
}
