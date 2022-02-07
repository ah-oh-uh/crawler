<?php

declare(strict_types=1);

namespace AgencyAnalytics\Crawler;

use AgencyAnalytics\LinkFilter\LinkFilter;
use AgencyAnalytics\LinksParser\LinksParserManager;
use AgencyAnalytics\PageStats\PageStats;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class PageScraper
{
    protected $httpClient;
    protected $linksParserManager;
    protected $pageStats;

    public function __construct(
        ClientInterface $httpClient,
        LinksParserManager $linksParserManager,
        PageStats $pageStats
    ) {
        $this->httpClient = $httpClient;
        $this->linksParserManager = $linksParserManager;
        $this->pageStats = $pageStats;
    }

    /**
     * Asynchroniously scrapes provided URL and returns a promise.
     */
    public function scrape(string $url): PromiseInterface
    {
        $url = (new LinkFilter($url))->filter($url);
        
        $this->linksParserManager->setBaseUrl($url);

        $startTime = time();

        $httpRequest = new Request('GET', $url);
        $promise = $this->httpClient->sendAsync($httpRequest);

        $promise = $promise->then(
            function (ResponseInterface $httpResponse) use ($url, $startTime) {
                return $this->parsePage($url, $startTime, $httpResponse);
            },
            function (RequestException $exception) use ($url, $startTime) {
                return $this->parsePage($url, $startTime, $exception->getResponse());
            }
        );

        return $promise;
    }

    /**
     * Returns page array with the properties suchs as code, url, links, stats.
     */
    protected function parsePage(string $url, int $startTime, ResponseInterface &$httpResponse): array
    {
        $contents = $httpResponse->getBody()->getContents();

        $page = [];
        $page['code'] = $httpResponse->getStatusCode();
        $page['url'] = $url;
        $page['links'] = $this->linksParserManager->parse($contents);
        $page['stats'] = $this->pageStats->getStats($startTime, $contents);

        return $page;
    }
}
