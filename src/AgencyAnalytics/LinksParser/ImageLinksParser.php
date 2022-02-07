<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

class ImageLinksParser extends LinksParser
{
    protected function getPattern(): string
    {
        return '/<img[^>]+src="(?P<link>[^"]+)"/i';
    }
}
