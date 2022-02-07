<?php

declare(strict_types=1);

namespace AgencyAnalytics\Report;

class CrawledPagesReport
{
    /**
     * Returns report for a given pages.
     */
    public function getReport(array $pages)
    {
        $intermediateReport = $this->getIntermediateReport($pages);
        $numberOfPages = count($pages);

        $report = [];
        $report['pages'] = $intermediateReport['pages'];
        $report['links'] = $this->getLinksCountPerType($intermediateReport['links']);
        $report['stats'] = $this->getAverageValuesPerType($intermediateReport['totals'], $numberOfPages);
        $report['stats']['pages'] = $numberOfPages;

        return $report;
    }

    /**
     * Returns intermediate reports with that will be used by getReport to prepare final report.
     */
    private function getIntermediateReport(array $pages): array
    {
        $reportPages = [];
        $reportTotals = [];
        $reportLinks = [];

        foreach ($pages as $page) {
            $reportLinks = array_replace_recursive($reportLinks, $page['links'] ?? []);
            $this->addTotalsPerType($reportTotals, $page['stats'] ?? []);
            $this->addPage($reportPages, $page);
        }

        $report = [
            'pages' => $reportPages,
            'totals' => $reportTotals,
            'links' => $reportLinks
        ];

        return $report;
    }

    /**
     * Adds page to report pages.
     */
    private function addPage(array &$reportPages, array $page): void
    {
        $reportPages[] = [
            'url' => $page['url'],
            'code' => $page['code'],
        ];
    }

    /**
     * Adds stats to totals.
     */
    private function addTotalsPerType(array &$reportTotals, array $stats): void
    {
        foreach ($stats as $type => $value) {
            $reportTotals[$type] = ($reportTotals[$type] ?? 0) + $value;
        }
    }

    /**
     * Returns array of link counts per type.
     */
    private function getLinksCountPerType(array $links): array
    {
        $linksCountPerType = [];
        foreach ($links as $type => $typeLinks) {
            $linksCountPerType[$type] = count($typeLinks);
        }

        return $linksCountPerType;
    }

    /**
     * Returns array of average values per type.
     */
    private function getAverageValuesPerType(array $totals, int $numberOfPages): array
    {
        $averageValuesPerType = [];
        foreach ($totals as $type => $value) {
            $averageValuesPerType[$type] = (int)round($value / $numberOfPages);
        }

        return $averageValuesPerType;
    }
}
