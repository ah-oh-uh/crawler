<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinkFilter;

interface LinkFilterInterface
{
    /**
     * Returns URL when it is accepted by a filter.
     */
    public function filter(string $url): ?string;
}
