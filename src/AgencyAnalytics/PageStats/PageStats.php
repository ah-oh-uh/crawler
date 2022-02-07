<?php

declare(strict_types=1);

namespace AgencyAnalytics\PageStats;

use AgencyAnalytics\PageStats\Counter\CounterInterface;

class PageStats
{
    protected $counters;

    public function __construct()
    {
        $this->counters = [];
    }

    /**
     * Adds counter to the list
     */
    public function addCounter(string $name, CounterInterface $counter): void
    {
        $this->counters[$name] = $counter;
    }

    /**
     * Returns array of counts
     */
    public function getStats(int $startTime, string $contents): array
    {
        $stats = [];
        foreach ($this->counters as $name => &$counter) {
            $stats[$name] = $counter->count($startTime, $contents);
        }

        return $stats;
    }
}
