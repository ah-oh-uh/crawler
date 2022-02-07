<?php

declare(strict_types=1);

namespace AgencyAnalytics\LinksParser;

class LinksParserManager implements LinksParserInterface
{
    private $parsers;
    private $baseUrl;


    public function __construct()
    {
        $this->parsers = [];
        $this->baseUrl = '';
    }

    /**
     * Sets base URL to all parsers.
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;

        foreach ($this->parsers as &$parser) {
            $parser->setBaseUrl($baseUrl);
        }
    }

    /**
     * Registers links parser with a given name.
     */
    public function addLinksParser(string $name, LinksParserInterface $linksParser): void
    {
        $linksParser->setBaseUrl($this->baseUrl);
        
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
