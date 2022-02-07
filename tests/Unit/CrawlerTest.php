<?php

namespace Tests\Unit;

use AgencyAnalytics\Crawler\Crawler;
use AgencyAnalytics\Crawler\PageScraperFactory;
use AgencyAnalytics\Report\CrawledPagesReport;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{
    private $entryPointUrl = 'http://test.com';
    private $maxPages = 3;

    private $page1 = "
        <title>Test title</title>
        <a href=\"/\">Home</a>
        <a href=\"/index.html\">Index</a>
        <a href=\"http://test.com/page1.html\">Page1</a>
        <a href=\"http://test2.com/page2.html\">Page2</a>
        <a href=\"http://test3.com/page3.html\">Page3</a>
        <a href=\"http://testX.com/pageX.html\">PageX</a>
        <img src=\"test.jpg\"/>
        <img src=\"http://test2.com/test.jpg\"/>
    ";

    private $page2 = "
        <title>Longer Test Title</title>
        <a href=\"/\">Home</a>
        <a href=\"/index.html\">Index</a>
        <a href=\"/test.html\">Index</a>
        <a href=\"http://test.com/page1.html\">Page1</a>
        <a href=\"http://test2.com/page2.html\">Page2</a>
        <a href=\"http://test3.com/page3.html\">Page3</a>
        <a href=\"http://test4.com/page4.html\">Page4</a>
        <a href=\"http://test5.com/page5.html\">Page5</a>
        <a href=\"http://test6.com/page6.html\">Page5</a>
        <img src=\"test.jpg\"/>
        <img src=\"http://test2.com/test.jpg\"/>
        <img src=\"http://test1.com/test1.jpg\"/>
        <img src=\"http://test2.com/test3.jpg\"/>
    ";

    private $page3 = "
        <title>Even Longer Test title</title>
        <a href=\"/\">Home</a>
        <a href=\"/index.html\">Index</a>
        <a href=\"http://test.com/page1.html\">Page1</a>
        <a href=\"http://test2.com/page2.html\">Page2</a>
        <a href=\"http://test3.com/page3.html\">Page3</a>
        <a href=\"http://test4.com/page4.html\">Page3</a>
        <a href=\"http://test5.com/page5.html\">Page3</a>
        <img src=\"test.jpg\"/>
        <img src=\"http://test2.com/test.jpg\"/>
    ";

    public function test_PageScraper()
    {
        $pageScraper = PageScraperFactory::create($this->getHttpClient());
        $response = $pageScraper->scrape($this->entryPointUrl)->wait();

        $this->assertTrue(count($response['links']['image']) === 2);
        $this->assertTrue(count($response['links']['internal']) === 3);
        $this->assertTrue(count($response['links']['external']) === 3);
        $this->assertTrue($response['stats']['page-load'] === 0);
        $this->assertTrue($response['stats']['words'] === 8);
        $this->assertTrue($response['stats']['title-length'] === 10);
    }

    public function test_Crawler()
    {
        $crawler = new Crawler(PageScraperFactory::create($this->getHttpClient()));
        $response = $crawler->crawl($this->entryPointUrl, $this->maxPages);

        $this->assertTrue(isset($response['http://test.com/']));
        $this->assertTrue($response['http://test.com/']['code'] === 200);
        $this->assertTrue(count($response['http://test.com/']['links']['image']) === 2);
        $this->assertTrue(count($response['http://test.com/']['links']['internal']) === 3);
        $this->assertTrue(count($response['http://test.com/']['links']['external']) === 3);
        $this->assertTrue($response['http://test.com/']['stats']['words'] === 8);
        $this->assertTrue($response['http://test.com/']['stats']['title-length'] === 10);

        $this->assertTrue(isset($response['http://test.com/index.html']));
        $this->assertTrue($response['http://test.com/index.html']['code'] === 404);
        $this->assertTrue(count($response['http://test.com/index.html']['links']['image']) === 4);
        $this->assertTrue(count($response['http://test.com/index.html']['links']['internal']) === 4);
        $this->assertTrue(count($response['http://test.com/index.html']['links']['external']) === 5);
        $this->assertTrue($response['http://test.com/index.html']['stats']['words'] === 12);
        $this->assertTrue($response['http://test.com/index.html']['stats']['title-length'] === 17);

        $this->assertTrue(isset($response['http://test.com/page1.html']));
        $this->assertTrue($response['http://test.com/page1.html']['code'] === 500);
        $this->assertTrue(count($response['http://test.com/page1.html']['links']['image']) === 2);
        $this->assertTrue(count($response['http://test.com/page1.html']['links']['internal']) === 3);
        $this->assertTrue(count($response['http://test.com/page1.html']['links']['external']) === 4);
        $this->assertTrue($response['http://test.com/page1.html']['stats']['words'] === 11);
        $this->assertTrue($response['http://test.com/page1.html']['stats']['title-length'] === 22);
    }

    public function test_CrawlerPagesReport()
    {
        $crawler = new Crawler(PageScraperFactory::create($this->getHttpClient()));
        $response = $crawler->crawl($this->entryPointUrl, $this->maxPages);

        $report = (new CrawledPagesReport())->getReport($response);

        $this->assertTrue($report['pages'][0]['url'] === 'http://test.com/');
        $this->assertTrue($report['pages'][0]['code'] === 200);
        $this->assertTrue($report['pages'][1]['url'] === 'http://test.com/index.html');
        $this->assertTrue($report['pages'][1]['code'] === 404);
        $this->assertTrue($report['pages'][2]['url'] === 'http://test.com/page1.html');
        $this->assertTrue($report['pages'][2]['code'] === 500);

        $this->assertTrue($report['links']['image'] === 4);
        $this->assertTrue($report['links']['internal'] === 4);
        $this->assertTrue($report['links']['external'] === 6);

        $this->assertTrue($report['stats']['words'] === 10);
        $this->assertTrue($report['stats']['title-length'] === 16);
        $this->assertTrue($report['stats']['pages'] === 3);
        $this->assertTrue($report['stats']['page-load'] === 0);
    }

    private function getHttpClient(): Client
    {
        $mock = new MockHandler([
            new Response(200, [], $this->page1),
            new Response(404, [], $this->page2),
            new Response(500, [], $this->page3),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = new Client(['handler' => $handlerStack]);

        return $client;
    }
}
