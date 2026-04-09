<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Monitorable
{
}
