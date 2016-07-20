<?php

namespace Purl\Test;

use PHPUnit_Framework_TestCase;
use Purl\Fragment;
use Purl\ParserInterface;
use Purl\Path;
use Purl\Query;
use Purl\Url;

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
/**
 * Class UrlTest
 *
 * @package Purl\Test
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $url = new Url();
        $url->setUrl('http://jwage.com');
        self::assertSame('http://jwage.com/', $url->getUrl());
        self::assertInstanceOf('Purl\Parser', $url->getParser());

        $parser = new TestParser();
        $url = new Url('http://jwage.com', $parser);
        self::assertSame($parser, $url->getParser());
    }

    public function testSetParser()
    {
        $parser = new TestParser();
        $url = new Url();
        $url->setParser($parser);
        self::assertSame($parser, $url->getParser());
    }

    public function testParseSanity()
    {
        $url = new Url('https://host.com:443/path with spaces?param1 with spaces=value1 with spaces&param2=value2#fragment1/fragment2 with spaces?param1=value1&param2 with spaces=value2 with spaces');
        self::assertSame('https', $url->scheme);
        self::assertSame('host.com', $url->host);
        self::assertSame('443', $url->port);
        self::assertInstanceOf('Purl\Path', $url->path);
        self::assertSame('/path%20with%20spaces', (string) $url->path);
        self::assertInstanceOf('Purl\Query', $url->query);
        self::assertSame('param1_with_spaces=value1+with+spaces&param2=value2', (string) $url->query);
        self::assertInstanceOf('Purl\Fragment', $url->fragment);
        self::assertSame('fragment1/fragment2%20with%20spaces?param1=value1&param2_with_spaces=value2+with+spaces', (string) $url->fragment);
        self::assertInstanceOf('Purl\Path', $url->fragment->path);
        self::assertInstanceOf('Purl\Query', $url->fragment->query);
        self::assertSame('param1=value1&param2_with_spaces=value2+with+spaces', (string) $url->fragment->query);
        self::assertSame('fragment1/fragment2%20with%20spaces', (string) $url->fragment->path);
    }

    public function testParseStaticMethod()
    {
        $url = Url::parse('http://google.com');
        self::assertInstanceOf('Purl\Url', $url);
        self::assertSame('http://google.com/', (string) $url);
    }

    public function testBuild()
    {
        $url = Url::parse('http://jwage.com')
            ->set('port', '443')
            ->set('scheme', 'https');

        $url->query
            ->set('param1', 'value1')
            ->set('param2', 'value2');

        $url->path->add('about');
        $url->path->add('me');

        $url->fragment->path->add('fragment1');
        $url->fragment->path->add('fragment2');

        $url->fragment->query
            ->set('param1', 'value1')
            ->set('param2', 'value2');

        self::assertSame('https://jwage.com:443/about/me?param1=value1&param2=value2#/fragment1/fragment2?param1=value1&param2=value2', (string) $url);
    }

    public function testJoin()
    {
        $url = new Url('http://jwage.com/about?param=value#fragment');
        self::assertSame('http://jwage.com/about?param=value#fragment', (string) $url);
        $url->join(new Url('http://about.me/jwage'));
        self::assertSame('http://about.me/jwage?param=value#fragment', (string) $url);
    }

    public function testSetPath()
    {
        $url = new Url('http://jwage.com');
        $url->path = 'about';
        self::assertInstanceOf('Purl\Path', $url->path);
        self::assertSame('about', (string) $url->path);
    }

    public function testSetQuery()
    {
        $url = new Url('http://jwage.com');
        $url->query->set('param1', 'value1');
        self::assertInstanceOf('Purl\Query', $url->query);
        self::assertSame('param1=value1', (string) $url->query);
        self::assertSame(array('param1' => 'value1'), $url->query->getData());
    }

    public function testSetFragment()
    {
        $url = new Url('http://jwage.com');
        $url->fragment->path = 'about';
        $url->fragment->query->set('param1', 'value1');
        self::assertSame('http://jwage.com/#about?param1=value1', (string) $url);
    }

    public function testGetNetloc()
    {
        $url = new Url('https://user:pass@jwage.com:443');
        self::assertSame('user:pass@jwage.com:443', $url->getNetloc());
    }

    public function testGetUrl()
    {
        $url = new Url('http://jwage.com');
        self::assertSame('http://jwage.com/', $url->getUrl());
    }

    public function testSetUrl()
    {
        $url = new Url('http://jwage.com');
        self::assertSame('http://jwage.com/', $url->getUrl());
        $url->setUrl('http://google.com');
        self::assertSame('http://google.com/', $url->getUrl());
    }

    public function testArrayAccess()
    {
        $url = new Url('http://jwage.com');
        $url['path'] = 'about';
        self::assertSame('http://jwage.com/about', (string) $url);
    }

    public function testCanonicalization()
    {
        $url = new Url('http://jwage.com');
        self::assertSame('com', $url->publicSuffix);
        self::assertSame('jwage.com', $url->registerableDomain);
        self::assertSame('com.jwage', $url->canonical);

        $url = new Url('http://sub.domain.jwage.com/index.php?param1=value1');
        self::assertSame('com', $url->publicSuffix);
        self::assertSame('jwage.com', $url->registerableDomain);
        self::assertSame('sub.domain', $url->subdomain);
        self::assertSame('com.jwage.domain.sub/index.php?param1=value1', $url->canonical);

        $url = new Url('http://sub.domain.jwage.co.uk/index.php?param1=value1');
        self::assertSame('co.uk', $url->publicSuffix);
        self::assertSame('jwage.co.uk', $url->registerableDomain);
        self::assertSame('sub.domain', $url->subdomain);
        self::assertSame('uk.co.jwage.domain.sub/index.php?param1=value1', $url->canonical);
    }

    public function testPath()
    {
        $url = new Url('http://jwage.com');
        $url->path->add('about')->add('me');
        self::assertSame('http://jwage.com/about/me', (string) $url);
        $url->path->setPath('new/path');
        self::assertSame('http://jwage.com/new/path', (string) $url);
    }

    public function testFragment()
    {
        $url = new Url('http://jwage.com');
        $url->setFragmentString('test');
        $url->fragment->path->add('about')->add('me');
        $url->fragment->query->set('param1', 'value1');
        self::assertSame('http://jwage.com/#test/about/me?param1=value1', (string) $url);

        $url->setFragmentString('test/aboutme?param1=value1');
        self::assertSame('test/aboutme', (string) $url->fragment->path);
        self::assertSame('param1=value1', (string) $url->fragment->query);
    }

    public function testQuery()
    {
        $url = new Url('http://jwage.com');
        $url->setQueryString('param1=value1&param2=value2');
        self::assertSame(array('param1' => 'value1', 'param2' => 'value2'), $url->query->getData());
        $url->query->set('param3', 'value3');
        self::assertSame('param1=value1&param2=value2&param3=value3', (string) $url->query);
    }

    public function testIsAbsolute()
    {
        $url1 = new Url('http://jwage.com');
        self::assertTrue($url1->isAbsolute());

        $url2 = new Url('/about/me');
        self::assertFalse($url2->isAbsolute());
    }

    public function testGetResource()
    {
        $url = new Url('http://jwage.com/about?query=value');
        self::assertSame('/about?query=value', $url->resource);
    }

    public function testPort()
    {
        $url = new Url('http://jwage.com:443');
        self::assertSame('443', $url->port);
        self::assertSame('http://jwage.com:443/', (string) $url);
    }

    public function testAuth()
    {
        $url = new Url('http://user:pass@jwage.com');
        self::assertSame('user', $url->user);
        self::assertSame('pass', $url->pass);
        self::assertSame('http://user:pass@jwage.com/', (string) $url);

        $url = new Url('http://user:@jwage.com');
        self::assertSame('user', $url->user);
        self::assertSame('', $url->pass);
        self::assertSame('http://user@jwage.com/', (string) $url);

        $url = new Url('http://user@jwage.com');
        self::assertSame('user', $url->user);
        self::assertSame(null, $url->pass);
        self::assertSame('http://user@jwage.com/', (string) $url);
    }

    public function testExtract()
    {
        $urls = Url::extract("test\nmore test https://google.com https://www.domain.de/foo.php?foobar=1&email=lars%40moelleken.org&guid=test1233312#bar öäü htp://test.de https:// http:// http://google.com\ntesting this out http://jwage.com more text https://we-are-a-professional-studio-of.photography");
        self::assertSame(5, count($urls));
        self::assertSame('https://google.com/', (string) $urls[0]);
        self::assertSame('https://www.domain.de/foo.php?foobar=1&email=lars%40moelleken.org&guid=test1233312#bar', (string) $urls[1]);
        self::assertSame('http://google.com/', (string) $urls[2]);
        self::assertSame('http://jwage.com/', (string) $urls[3]);
        self::assertSame('https://we-are-a-professional-studio-of.photography/', (string) $urls[4]);
    }

    public function testManualObjectConstruction()
    {
        $url = new Url('http://jwage.com');
        $url->set('path', new Path('about'));
        $url->set('query', new Query('param=value'));
        $url->set('fragment', new Fragment(new Path('about'), new Query('param=value')));
        self::assertSame('http://jwage.com/about?param=value#about?param=value', (string) $url);
    }

    public function testSetPathString()
    {
        $url = new Url('http://jwage.com');
        $url->setPathString('about');
        $url->setQuery(new Query('param=value'));
        $url->setFragment(new Fragment(new Path('about'), new Query('param=value')));
        self::assertSame('http://jwage.com/about?param=value#about?param=value', (string) $url);
    }

    public function testIdeGettersAndSetters()
    {
        $url = new Url('http://jwage.com');
        $url->setPath(new Path('about'));
        $url->setQuery(new Query('param=value'));
        $url->setFragment(new Fragment(new Path('about'), new Query('param=value')));
        self::assertSame('http://jwage.com/about?param=value#about?param=value', (string) $url);
    }

    public function testFromCurrentServerVariables() {
        $_SERVER['HTTP_HOST'] = 'jwage.com';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/about';

        $url = Url::fromCurrent();
        self::assertSame('http://jwage.com/about', (string) $url);

        $_SERVER['REQUEST_URI'] = '/about?param=value';

        $url = Url::fromCurrent();
        self::assertSame('http://jwage.com/about?param=value', (string) $url);

        $_SERVER['HTTPS'] = 'off';
        $_SERVER['HTTP_HOST'] = 'jwage.com';
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);

        $url = Url::fromCurrent();
        self::assertSame('http://jwage.com/', (string) $url);

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'jwage.com';
        $_SERVER['SERVER_PORT'] = 443;
        unset($_SERVER['REQUEST_URI']);

        $url = Url::fromCurrent();
        self::assertSame('https://jwage.com/', (string) $url);

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'jwage.com';
        $_SERVER['SERVER_PORT'] = 8080;
        unset($_SERVER['REQUEST_URI']);

        $url = Url::fromCurrent();
        self::assertSame('http://jwage.com:8080/', (string) $url);

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'jwage.com';
        $_SERVER['SERVER_PORT'] = 80;
        unset($_SERVER['REQUEST_URI']);
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'passwd123';

        $url = Url::fromCurrent();
        self::assertSame('http://user:passwd123@jwage.com/', (string) $url);
    }
    
    public function testRelativeUrl()
    {
        // test all resource parts
        $url = new Url('/path1/path2?x=1&y=2#frag');
        self::assertFalse($url->isAbsolute());
        self::assertSame('/path1/path2?x=1&y=2#frag', (string)$url);
        
        // test base path
        $url = new Url('/path1');
        self::assertSame('/path1', (string)$url);
        
        // test minimal path
        $url = new Url('/');
        self::assertSame('/', (string)$url);
        
        // test feature request
        $url = new Url('/events');
        $url->query->set('param1', 'value1');
        self::assertSame('/events?param1=value1', (string)$url);
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
/**
 * Class TestParser
 *
 * @package Purl\Test
 */
class TestParser implements ParserInterface
{
    /**
     * @param Url|string $url
     *
     * @return Url|string
     */
    public function parseUrl($url)
    {
        return $url;
    }
}
