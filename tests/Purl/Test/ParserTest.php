<?php

namespace Purl\Test;

use Pdp\Parser as PslParser;
use Pdp\PublicSuffixListManager;
use PHPUnit_Framework_TestCase;
use Purl\Parser;

/**
 * Class ParserTest
 *
 * @package Purl\Test
 */
class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp()
    {
        parent::setUp();
        $pslManager = new PublicSuffixListManager(dirname(dirname(dirname(__DIR__))) . '/data');
        $pslParser = new PslParser($pslManager->getList());
        $this->parser = new Parser($pslParser);
    }

    protected function tearDown()
    {
        $this->parser = null;
        parent::tearDown();
    }

    public function testParseUrl()
    {
        $parts = $this->parser->parseUrl('https://sub.domain.jwage.com:443/about?param=value#fragment?param=value');
        self::assertSame(array(
            'scheme' => 'https',
            'host' => 'sub.domain.jwage.com',
            'port' => '443',
            'user' => null,
            'pass' => null,
            'path' => '/about',
            'query' => 'param=value',
            'fragment' => 'fragment?param=value',
            'publicSuffix' => 'com',
            'registerableDomain' => 'jwage.com',
            'subdomain' => 'sub.domain',
            'canonical' => 'com.jwage.domain.sub/about?param=value',
            'resource' => '/about?param=value',
            'registrableDomain' => 'jwage.com'
        ), $parts);
    }
    
    public function testParseBadUrlThrowsInvalidArgumentException()
    {
        $this->setExpectedException('\InvalidArgumentException', '"http:///example.com" is one seriously malformed url.');
        $this->parser->parseUrl('http:///example.com');
    }
}
