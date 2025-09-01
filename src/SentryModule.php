<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use InvalidArgumentException;
use Pj8\SentryModule\Annotation\Monitorable;
use Psr\Log\LoggerInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;
use Sentry\Integration\IntegrationInterface;

use function array_key_exists;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class SentryModule extends AbstractModule
{
    /** @var array{dsn?: string|null, environment?: string|null, release?: string|null, sample_rate?: float|int, traces_sample_rate?: float|int|null, profiles_sample_rate?: float|int|null, send_default_pii?: bool, server_name?: string, in_app_exclude?: array<array-key, string>, in_app_include?: array<array-key, string>, integrations?: array<array-key, IntegrationInterface>|callable, default_integrations?: bool, before_send?: callable, before_send_transaction?: callable, before_breadcrumb?: callable, trace_propagation_targets?: array<array-key, string>|null, attach_stacktrace?: bool, context_lines?: int|null, enable_logs?: bool, logger?: LoggerInterface|null, spotlight?: bool, spotlight_url?: string} Sentry SDK 初期化オプション */
    private array $config;

    /**
     * @param array{dsn?: string|null, environment?: string|null, release?: string|null, sample_rate?: float|int, traces_sample_rate?: float|int|null, profiles_sample_rate?: float|int|null, send_default_pii?: bool, server_name?: string, in_app_exclude?: array<array-key, string>, in_app_include?: array<array-key, string>, integrations?: array<array-key, IntegrationInterface>|callable, default_integrations?: bool, before_send?: callable, before_send_transaction?: callable, before_breadcrumb?: callable, trace_propagation_targets?: array<array-key, string>|null, attach_stacktrace?: bool, context_lines?: int|null, enable_logs?: bool, logger?: LoggerInterface|null, spotlight?: bool, spotlight_url?: string} $config Sentry SDK 初期化オプション
     *
     * @see https://docs.sentry.io/platforms/php/configuration/options/
     * @see Sentry::Init()
     */
    public function __construct(array $config)
    {
        $this->guardInvalid($config);
        $this->config = $config;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->bind()->annotatedWith('sentry-options')->toInstance($this->config);
        $this->bind(CliNameBuilder::class);
        $this->bind(WebNameBuilder::class);
        $this->bind()->annotatedWith('sentry-tr-name')->toProvider(TransactionNameProvider::class);
        $this->bind(TransactionInterface::class)->to(Transaction::class)->in(Scope::SINGLETON);
        $this->bind(SpanInterface::class)->to(Span::class);
        $this->bind(SpanContextFactoryInterface::class)->to(SpanContextFactory::class);
        $this->bind(ResourceSpanFactory::class);

        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Monitorable::class),
            [MonitorInterceptor::class],
        );
    }

    /** @param array{dsn?: string|null, environment?: string|null, release?: string|null, sample_rate?: float|int, traces_sample_rate?: float|int|null, profiles_sample_rate?: float|int|null, send_default_pii?: bool, server_name?: string, in_app_exclude?: array<array-key, string>, in_app_include?: array<array-key, string>, integrations?: array<array-key, IntegrationInterface>|callable, default_integrations?: bool, before_send?: callable, before_send_transaction?: callable, before_breadcrumb?: callable, trace_propagation_targets?: array<array-key, string>|null, attach_stacktrace?: bool, context_lines?: int|null, enable_logs?: bool, logger?: LoggerInterface|null, spotlight?: bool, spotlight_url?: string} $config */
    private function guardInvalid(array $config): void
    {
        if (! array_key_exists('dsn', $config)) {
            throw new InvalidArgumentException();
        }
    }
}
