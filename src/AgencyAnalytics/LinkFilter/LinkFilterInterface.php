<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinkFilter;

interface LinkFilterInterface
{
    /**
     * Sets base URL that is used to rebuild relative links.
     */
    public function setBaseUrl(string $baseUrl): void;

    /**
     * Returns URL when it is accepted by a filter.
     */
    public function filter(string $url): ?string;
}
