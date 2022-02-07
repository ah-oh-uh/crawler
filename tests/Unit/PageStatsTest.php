<?php

namespace Tests\Unit;

use AgencyAnalytics\PageStats\Counter\PageLoadCounter;
use AgencyAnalytics\PageStats\Counter\TitleLength;
use AgencyAnalytics\PageStats\Counter\WordsCounter;
use AgencyAnalytics\PageStats\PageStats;
use PHPUnit\Framework\TestCase;

class PageStatsTest extends TestCase
{
    public function test_PageLoadCounter()
    {
        $startTime = time();
        sleep(1);

        $pageLoadCounter = new PageLoadCounter();
        $count = $pageLoadCounter->count($startTime, '');

        $this->assertTrue($count === 1);
    }

    public function test_TitleLength()
    {
        $contents = "Some text\n<title>Title length is 18</title>";

        $titleLengthCounter = new TitleLength();
        $titleLength = $titleLengthCounter->count(0, $contents);

        $this->assertTrue($titleLength === 18);
    }

    public function test_WordsCounter()
    {
        $contents = "This <strong>is a test</strong> text to see <i>if we can count words</i>";

        $wordsCounter = new WordsCounter();
        $numberOfWords = $wordsCounter->count(0, $contents);

        $this->assertTrue($numberOfWords === 12);
    }

    public function test_PageStats()
    {
        $pageStats = new PageStats();
        $pageStats->addCounter('page-load', new PageLoadCounter());
        $pageStats->addCounter('words', new WordsCounter());
        $pageStats->addCounter('title-length', new TitleLength());


        $contents = "
            <html>
                <title>Test title</title>
                <body>
                    This <strong>is a test</strong> text to see <i>if we can count words</i>
                </body>
            </html>";
        
        $startTime = time();
        sleep(1);

        $stats = $pageStats->getStats($startTime, $contents);

        $this->assertTrue($stats['page-load'] === 1);
        $this->assertTrue($stats['words'] === 14);
        $this->assertTrue($stats['title-length'] === 10);
    }
}
