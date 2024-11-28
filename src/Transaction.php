<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Ray\Di\Di\Named;
use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\Transaction as SentryTransaction;
use Sentry\Tracing\TransactionContext;

use function Sentry\init;
use function Sentry\startTransaction;

final class Transaction implements TransactionInterface
{
    private string $transactionName;
    private SentryTransaction $transaction;
    private static string $operation = 'backend';

    /**
     * @param array<string,mixed> $options
     *
     * @Named("options=sentry-options,name=sentry-tr-name")
     */
    #[Named('options=sentry-options,name=sentry-tr-name')]
    public function __construct(private array $options, string $name)
    {
        $this->transactionName = $name;
        $this->startTransaction();
    }

    public function __destruct()
    {
        $this->finishTransaction();
    }

    private function startTransaction(): void
    {
        init($this->options);

        $transactionContext = new TransactionContext();
        $transactionContext->setName($this->transactionName);
        $transactionContext->setOp(self::$operation);
        $this->transaction = startTransaction($transactionContext);
    }

    private function finishTransaction(): void
    {
        $this->transaction->finish();
    }

    public function startChild(SpanContext $context): TracingSpan
    {
        return $this->transaction->startChild($context);
    }

    public function getTransaction(): SentryTransaction
    {
        return $this->transaction;
    }
}
