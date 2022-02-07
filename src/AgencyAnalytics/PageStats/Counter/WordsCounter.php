<?php

declare(strict_types=1);

namespace AgencyAnalytics\PageStats\Counter;

class WordsCounter implements CounterInterface
{
    /**
     * Returns number of words in a given text.
     */
    public function count(int $startTime, string $contents): int
    {
        $contents = html_entity_decode(strip_tags($contents));

        return str_word_count($contents);
    }
}
