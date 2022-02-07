<?php

namespace Tests\Unit;

use AgencyAnalytics\LinkFilter\InternalLinkFilter;
use AgencyAnalytics\LinkFilter\ExternalLinkFilter;
use AgencyAnalytics\LinkFilter\LinkFilter;
use AgencyAnalytics\LinksParser\ImageLinksParser;
use AgencyAnalytics\LinksParser\LinksParser;
use PHPUnit\Framework\TestCase;

class LinksParserTest extends TestCase
{
    private $contents = "
        <a href=\"/\">Home</a>
        <a href=\"/index.html\">Index</a>
        <a href=\"http://test.com/page1.html\">Page1</a>
        <a href=\"http://test2.com/page2.html\">Page2</a>
        <a href=\"http://test3.com/page3.html\">Page3</a>
        <img src=\"test.jpg\"/>
        <img src=\"http://test2.com/test.jpg\"/>
    ";

    public function test_InternalLinksParser()
    {
        $linksParser = new LinksParser(new InternalLinkFilter('http://test.com'));
        $links = $linksParser->parse($this->contents);

        $this->assertTrue(count($links) === 3);
        $this->assertTrue(isset($links['http://test.com/']));
        $this->assertTrue(isset($links['http://test.com/index.html']));
        $this->assertTrue(isset($links['http://test.com/page1.html']));
    }

    public function test_ExternalLinksParser()
    {
        $linksParser = new LinksParser(new ExternalLinkFilter('http://test.com'));
        $links = $linksParser->parse($this->contents);

        $this->assertTrue(count($links) === 2);
        $this->assertTrue(isset($links['http://test2.com/page2.html']));
        $this->assertTrue(isset($links['http://test3.com/page3.html']));
    }

    public function test_LinksParser()
    {
        $linksParser = new LinksParser(new LinkFilter('http://test.com'));
        $links = $linksParser->parse($this->contents);

        $this->assertTrue(count($links) === 5);
    }

    public function test_ImageLinksParser()
    {
        $linksParser = new ImageLinksParser(new LinkFilter('http://test.com'));
        $links = $linksParser->parse($this->contents);

        $this->assertTrue(count($links) === 2);
        $this->assertTrue(isset($links['http://test.com/test.jpg']));
        $this->assertTrue(isset($links['http://test2.com/test.jpg']));       
    }
}
