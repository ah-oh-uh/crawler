<?php

declare(strict_types=1);

namespace AgencyAnalytics\Crawler;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;

class Crawler
{
    protected $pageScraper;

    public function __construct(PageScraper $pageScraper)
    {
        $this->pageScraper = $pageScraper;
    }

    /**
     * Crawls provided entry point URL including a number of internal links
     * specified by maxPages parameter.
     */
    public function crawl(string $entryPointUrl, int $maxPages): array
    {
        $entryPointUrl = rtrim($entryPointUrl, '/');

        $promise = $this->pageScraper->scrape($entryPointUrl);
        $promise = $promise->then(
            function (array $page) use ($maxPages) {
                return $this->crawlInternalPageLinks($page, $maxPages);
            }
        );

        return $promise->wait();
    }

    /**
     * Crawls internal page links of a given page with up to provided maxPages.
     */
    protected function crawlInternalPageLinks(array $page, int $maxPages): PromiseInterface
    {
        $urls = $page['links']['internal'] ?? [];
        $pageUrl = $page['url'] ?? '';
        if (isset($urls[$pageUrl])) {
            unset($urls[$pageUrl]);
        }

        $promise = $this->crawlUrls($urls, $maxPages - 1);
        $promise = $promise->then(
            function (array $pages) use ($page) {
                return $this->injectPage($pages, $page);
            }
        );

        return $promise;
    }

    /**
     * Injects page at the beginning of a given list of pages.
     */
    protected function injectPage(array $pages, array $page): array
    {
        $newPages = [];
        $newPages[$page['url']] = $page;
        $newPages += $pages;

        return $newPages;
    }

    /**
     * Asynchroniously crawls provided list of URL with a given PageScraper.
     */
    protected function crawlUrls(array $urls, int $maxPages): PromiseInterface
    {
        $promises = [];
        foreach ($urls as $url) {
            if (count($promises) >= $maxPages) {
                break;
            }

            $promises[$url] = $this->pageScraper->scrape($url);
        }

        return Utils::all($promises);
    }
}
