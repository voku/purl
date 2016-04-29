<?php

namespace Purl\Test;

use PHPUnit_Framework_TestCase;
use Purl\Path;

/**
 * Class PathTest
 *
 * @package Purl\Test
 */
class PathTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $path = new Path('test');
        self::assertEquals('test', $path->getPath());
    }

    public function testGetSetPath()
    {
        $path = new Path();
        self::assertEquals('', $path->getPath());
        $path->setPath('test');
        self::assertEquals('test', $path->getPath());
    }

    public function testGetSegments()
    {
        $path = new Path('about/me');
        self::assertEquals(array('about', 'me'), $path->getSegments());
    }

    public function testToString()
    {
        $path = new Path('about/me');
        self::assertEquals('about/me', (string) $path);
    }
}
