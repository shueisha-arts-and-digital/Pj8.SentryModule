<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Sunday\Extension\Error\ErrorInterface;
use Ray\Di\AbstractModule;

class SentryErrorModule extends AbstractModule
{
    protected function configure()
    {
        $this->rename(ErrorInterface::class, 'original');
        $this->bind(ErrorInterface::class)->to(SentryErrorHandler::class);
    }
}
