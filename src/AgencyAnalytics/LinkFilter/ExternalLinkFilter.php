<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinkFilter;

class ExternalLinkFilter extends LinkFilter
{
    /**
     * Returns true when URL's host is not equal to the base host.
     */
    protected function canAccept(array $urlParts): bool
    {
        return !empty($urlParts['host']) && $urlParts['host'] !== $this->baseUrlParts['host'];
    }
}
