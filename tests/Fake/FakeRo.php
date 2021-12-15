<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Fake;

use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;

class FakeRo extends ResourceObject
{
    private FakeBaz $baz;

    public function __construct(FakeBaz $baz)
    {
        $this->baz = $baz;
        // @see \Pj8\SentryModule\ResourceSpanFactory
        $this->uri = new Uri('app://foo/bar');
    }

    public function bar(): void
    {
    }

    public function onGet(): ResourceObject
    {
        $this->baz->onGet();
        $this->baz->foo();
        return $this;
    }

    public function onPost(): void
    {
    }

    public function onPut(): void
    {
    }

    public function onDelete(): void
    {
    }
}
