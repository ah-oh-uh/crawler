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
use GuzzleHttp\Client;

class PageScraperFactory
{
	public static function create(Client $httpClient): PageScraper
	{
        $linksParserManager = new LinksParserManager();
        $linksParserManager->addLinksParser('image', new ImageLinksParser(new LinkFilter()));
        $linksParserManager->addLinksParser('internal', new LinksParser(new InternalLinkFilter()));
        $linksParserManager->addLinksParser('external', new LinksParser(new ExternalLinkFilter()));

        $pageStats = new PageStats();
        $pageStats->addCounter('page-load', new PageLoadCounter());
        $pageStats->addCounter('words', new WordsCounter());
        $pageStats->addCounter('title-length', new TitleLength());

        $pageScraper = new PageScraper($httpClient, $linksParserManager, $pageStats);

		return $pageScraper;
	}
}