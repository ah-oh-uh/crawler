<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinkFilter;

class LinkFilter implements LinkFilterInterface
{
    protected $baseUrlParts;

    public function __construct(string $baseUrl)
    {
        $this->baseUrlParts = parse_url($baseUrl);

        if (empty($this->baseUrlParts['scheme'])) {
            $this->baseUrlParts['scheme'] = 'http';
        }

        if (empty($this->baseUrlParts['host'])) {
            $this->baseUrlParts['host'] = '';
        }

        if (empty($this->baseUrlParts['path'])) {
            $this->baseUrlParts['path'] = '';
        }
    }

    /**
     * Rebuilds and returns absolute URL.
     */
    public function filter(string $url): ?string
    {
        $absoluteUrl = null;
        $urlParts = parse_url($url);

        if ($this->canAccept($urlParts)) {
            $absoluteUrl = $this->getAbsoluteUrl($urlParts);
        }

        return $absoluteUrl;
    }

    /**
     * Returns absolute url for the URL parts.
     */
    public function getAbsoluteUrl(array $urlParts): string
    {
        if (empty($urlParts['scheme'])) {
            $urlParts['scheme'] = $this->baseUrlParts['scheme'];
        }

        if (empty($urlParts['host'])) {
            $urlParts['host'] = $this->baseUrlParts['host'];
        }

        if (empty($urlParts['path'])) {
            $urlParts['path'] = $this->baseUrlParts['path'];
        }

        $absoluteUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . '/' . ltrim($urlParts['path'], '/');

        if (isset($urlParts['query'])) {
            $absoluteUrl .= '?' . $urlParts['query'];
        }

        return $absoluteUrl;
    }

    /**
     * Returns true when given URL is can be accepted.
     */
    protected function canAccept(array $urlParts): bool
    {
        return true;
    }
}
