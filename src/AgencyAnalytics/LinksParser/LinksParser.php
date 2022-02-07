<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

use AgencyAnalytics\LinkFilter\LinkFilterInterface;

class LinksParser implements LinksParserInterface
{
    protected $linkFilter;

    public function __construct(LinkFilterInterface $linkFilter)
    {
        $this->linkFilter = $linkFilter;
    }

    /**
     * Sets base URL that is used to rebuild relative links.
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->linkFilter->setBaseUrl($baseUrl);
    }

    /**
     * Returns array of links parsed from a given contents.
     */
    public function parse(string $contents): array
    {
        $links = [];
        $offset = 0;
        $matches = [];
        $pattern = $this->getPattern();

        while (preg_match($pattern, $contents, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $offset = $matches[0][1] + strlen($matches[0][0]);
            $link = $matches['link'][0];
            $link = $this->linkFilter->filter($link);

            if (empty($link)) {
                continue;
            }

            $links[$link] = $link;
        }

        return $links;
    }

    /**
     * Return links regex pattern.
     */
    protected function getPattern(): string
    {
        return '/<a[^>]+href="(?P<link>[^"]+)"/i';
    }
}
