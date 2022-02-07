<?php

declare(strict_types=1);

namespace AgencyAnalytics\PageStats\Counter;

class TitleLength implements CounterInterface
{
    /**
     * Returns number of characters in HTML title tag.
     */
    public function count(int $startTime, string $contents): int
    {
        $length = 0;

        if (preg_match('/<title>([^>]+)<\/title>/i', $contents, $matches)) {
            $length = strlen(trim($matches[1]));
        }

        return $length;
    }
}
