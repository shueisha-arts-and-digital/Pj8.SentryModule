<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Psr\Log\LoggerInterface;
use Ray\Di\Di\Named;
use Sentry\Integration\IntegrationInterface;
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
     * @param array{dsn?: string|null, environment?: string|null, release?: string|null, sample_rate?: float|int, traces_sample_rate?: float|int|null, profiles_sample_rate?: float|int|null, send_default_pii?: bool, server_name?: string, in_app_exclude?: array<array-key, string>, in_app_include?: array<array-key, string>, integrations?: array<array-key, IntegrationInterface>|callable, default_integrations?: bool, before_send?: callable, before_send_transaction?: callable, before_breadcrumb?: callable, trace_propagation_targets?: array<array-key, string>|null, attach_stacktrace?: bool, context_lines?: int|null, enable_logs?: bool, logger?: LoggerInterface|null, spotlight?: bool, spotlight_url?: string} $options
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
