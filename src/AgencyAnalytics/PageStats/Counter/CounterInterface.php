<?php

declare(strict_types=1);

namespace AgencyAnalytics\PageStats\Counter;

interface CounterInterface
{
    /**
     * Returns count for a given contents and startTime.
     */
    public function count(int $startTime, string $contents): int;
}
