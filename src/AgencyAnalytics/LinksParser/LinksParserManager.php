<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

class LinksParserManager implements LinksParserInterface
{
    protected $parsers;

    public function __construct()
    {
        $this->parsers = [];
    }

    /**
     * Registers links parser with a given name.
     */
    public function addLinksParser(string $name, LinksParserInterface $linksParser): void
    {
        $this->parsers[$name] = $linksParser;
    }

    /**
     * Returns array of parsed links per parser name.
     */
    public function parse(string $contents): array
    {
        $result = [];
        foreach ($this->parsers as $name => &$parser) {
            $result[$name] = $parser->parse($contents);
        }

        return $result;
    }
}
