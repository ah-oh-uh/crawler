<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

interface LinksParserInterface
{
    /**
     * Sets base URL that is used to rebuild relative links.
     */
    public function setBaseUrl(string $baseUrl): void;

    /**
     * Returns array of links parsed from a given contents.
     */
    public function parse(string $contents): array;
}
