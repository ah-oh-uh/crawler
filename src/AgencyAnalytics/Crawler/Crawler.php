<?php

declare(strict_types=1);

namespace AgencyAnalytics\Crawler;

use AgencyAnalytics\LinkFilter\ExternalLinkFilter;
use AgencyAnalytics\LinkFilter\InternalLinkFilter;
use AgencyAnalytics\LinkFilter\LinkFilter;
use AgencyAnalytics\LinksParser\ImageLinksParser;
use AgencyAnalytics\LinksParser\LinksParser;
use AgencyAnalytics\LinksParser\LinksParserManager;
use AgencyAnalytics\PageStats\Counter\PageLoadCounter;
use AgencyAnalytics\PageStats\Counter\TitleLength;
use AgencyAnalytics\PageStats\Counter\WordsCounter;
use AgencyAnalytics\PageStats\PageStats;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;

class Crawler
{
    protected $httpClient;

    public function __construct(ClientInterface $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Crawls provided entry point URL including a number of internal links
     * specified by maxPages parameter.
     */
    public function crawl(string $entryPointUrl, int $maxPages): array
    {
        $entryPointUrl = rtrim($entryPointUrl, '/');
        $pageScraper = $this->createPageScraper($entryPointUrl);

        $promise = $pageScraper->scrape($entryPointUrl);
        $promise = $promise->then(
            function (array $page) use (&$pageScraper, $maxPages) {
                return $this->crawlInternalPageLinks($pageScraper, $page, $maxPages);
            }
        );

        return $promise->wait();
    }

    /**
     * Creates instance of PageScraper for a given URL.
     */
    protected function createPageScraper(string $url): PageScraper
    {
        $linksParserManager = new LinksParserManager();
        $linksParserManager->addLinksParser('image', new ImageLinksParser(new LinkFilter($url)));
        $linksParserManager->addLinksParser('internal', new LinksParser(new InternalLinkFilter($url)));
        $linksParserManager->addLinksParser('external', new LinksParser(new ExternalLinkFilter($url)));

        $pageStats = new PageStats();
        $pageStats->addCounter('page-load', new PageLoadCounter());
        $pageStats->addCounter('words', new WordsCounter());
        $pageStats->addCounter('title-length', new TitleLength());

        $pageScraper = new PageScraper($this->httpClient, $linksParserManager, $pageStats);

        return $pageScraper;
    }

    /**
     * Crawls internal page links of a given page with up to provided maxPages.
     */
    protected function crawlInternalPageLinks(PageScraper &$pageScraper, array $page, int $maxPages): PromiseInterface
    {
        $urls = $page['links']['internal'] ?? [];
        $pageUrl = $page['url'] ?? '';
        if (isset($urls[$pageUrl])) {
            unset($urls[$pageUrl]);
        }

        $promise = $this->crawlUrls($pageScraper, $urls, $maxPages - 1);
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
    protected function crawlUrls(PageScraper &$pageScraper, array $urls, int $maxPages): PromiseInterface
    {
        $promises = [];
        foreach ($urls as $url) {
            if (count($promises) >= $maxPages) {
                break;
            }

            $promises[$url] = $pageScraper->scrape($url);
        }

        return Utils::all($promises);
    }
}
