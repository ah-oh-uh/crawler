<?php

namespace App\Http\Controllers;

use AgencyAnalytics\Crawler\Crawler;
use AgencyAnalytics\Crawler\PageScraperFactory;
use AgencyAnalytics\Report\CrawledPagesReport;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    public function get()
    {
        return view('crawler-form');
    }

    public function post(Request $request)
    {
        $data = $request->validate([
            'url' => 'required|url',
            'maxPages' => 'required|int',
        ]);

        if (!empty($data['url']) && $data['maxPages'] > 0) {
            return $this->crawl($data['url'], $data['maxPages']);
        }

        return view('crawler-form');
    }

    private function crawl(string $url, int $maxPages): \Illuminate\Contracts\View\View
    {
        $crawler = new Crawler(PageScraperFactory::create(new Client()));
        $pages = $crawler->crawl($url, $maxPages);

        $report = (new CrawledPagesReport())->getReport($pages);

        return view('crawler-report', $report);
    }
}
