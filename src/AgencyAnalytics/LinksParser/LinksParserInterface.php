<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

interface LinksParserInterface
{
    /**
     * Returns array of links parsed from a given contents.
     */
    public function parse(string $contents): array;
}
