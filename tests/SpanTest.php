<?php

namespace Pj8\SentryModule;

use LogicException;
use PHPUnit\Framework\TestCase;
use Sentry\Tracing\Span as SentrySpan;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\SpanStatus;

class SpanTest extends TestCase
{
    public function testStartCreateElementCaseEmpty(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);

        $result = $span->getCurrentSpan();
        $this->assertNotNull($result);
        $this->assertInstanceOf(SentrySpan::class, $result);
    }

    public function testGetCurrentSpanReturnsNullCaseEmpty(): void
    {
        $span = $this->createSpan();
        $result = $span->getCurrentSpan();
        $this->assertNull($result);
    }

    public function testIsFirstReturnsTrueCaseFirstTransaction(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);

        $result = $span->isFirst();
        $this->assertTrue($result);
    }

    public function testIsFirstReturnsFalseCase2ndSpan(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);
        $span->start($fixture);

        $result = $span->isFirst();
        $this->assertFalse($result);
    }

    public function testSetCurrentSpanUpdateStatus(): void
    {
        $span = $this->createSpan();
        $dummy = new SpanContext();
        $span->start($dummy);

        $fixture = 404;
        $status = SpanStatus::createFromHttpStatusCode($fixture);
        $tracingSpan = $span->getCurrentSpan();
        if ($tracingSpan === null) {
            throw new LogicException();
        }

        /** @psalm-suppress StaticAccess */
        $tracingSpan->setStatus($status);

        $span->setCurrentSpan($tracingSpan);

        $current = $span->getCurrentSpan();
        if ($current === null) {
            throw new LogicException();
        }

        $result = $current->getStatus();
        $this->assertSame($status, $result);
    }

    private function createSpan(): Span
    {
        $dryRun = ['dsn' => null];
        $transaction = new Transaction($dryRun, 'dummy');

        return new Span($transaction);
    }
}
