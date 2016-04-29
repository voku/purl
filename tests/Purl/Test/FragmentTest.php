<?php

namespace Purl\Test;

use PHPUnit_Framework_TestCase;
use Purl\Fragment;
use Purl\Path;
use Purl\Query;

/**
 * Class FragmentTest
 *
 * @package Purl\Test
 */
class FragmentTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $fragment = new Fragment('test?param=value');
        self::assertInstanceOf('Purl\Path', $fragment->path);
        self::assertInstanceOf('Purl\Query', $fragment->query);
        self::assertEquals('test', (string) $fragment->path);
        self::assertEquals('param=value', (string) $fragment->query);

        $path = new Path('test');
        $query = new Query('param=value');
        $fragment = new Fragment($path, $query);
        self::assertEquals('test', (string) $fragment->path);
        self::assertEquals('param=value', (string) $fragment->query);
    }

    public function testGetFragment()
    {
        $fragment = new Fragment('test?param=value');
        self::assertEquals('test?param=value', $fragment->getFragment());
    }

    public function testSetFragment()
    {
        $fragment = new Fragment('test?param=value');
        self::assertEquals('test?param=value', $fragment->getFragment());
        $fragment->setFragment('changed?param=value');
        self::assertEquals('changed?param=value', $fragment->getFragment());
    }

    public function testGetSetPath()
    {
        $fragment = new Fragment();
        $path = new Path('test');
        $fragment->setPath($path);
        self::assertSame($path, $fragment->getPath());
        self::assertEquals('test', (string) $fragment);
    }

    public function testGetSetQuery()
    {
        $fragment = new Fragment();
        $query = new Query('param=value');
        $fragment->setQuery($query);
        self::assertSame($query, $fragment->getQuery());
        self::assertEquals('?param=value', (string) $fragment);
    }

    public function testToString()
    {
        $fragment = new Fragment('test?param=value');
        self::assertEquals('test?param=value', (string) $fragment);
    }
}
