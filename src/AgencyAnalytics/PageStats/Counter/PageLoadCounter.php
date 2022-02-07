<?php

declare(strict_types=1);

namespace AgencyAnalytics\PageStats\Counter;

class PageLoadCounter implements CounterInterface
{
    /**
     * Returns a difference between now and start time.
     */
    public function count(int $startTime, string $contents): int
    {
        return time() - $startTime;
    }
}
