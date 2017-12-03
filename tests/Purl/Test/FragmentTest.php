<?php

namespace Purl\Test;

use PHPUnit\Framework\TestCase;
use Purl\Fragment;
use Purl\Path;
use Purl\Query;

/**
 * Class FragmentTest
 *
 * @package Purl\Test
 */
class FragmentTest extends TestCase
{
  public function testConstruct()
  {
    $fragment = new Fragment('test?param=value');
    self::assertInstanceOf('Purl\Path', $fragment->path);
    self::assertInstanceOf('Purl\Query', $fragment->query);
    self::assertSame('test', (string)$fragment->path);
    self::assertSame('param=value', (string)$fragment->query);

    $path = new Path('test');
    $query = new Query('param=value');
    $fragment = new Fragment($path, $query);
    self::assertSame('test', (string)$fragment->path);
    self::assertSame('param=value', (string)$fragment->query);
  }

  public function testGetFragment()
  {
    $fragment = new Fragment('test?param=value');
    self::assertSame('test?param=value', $fragment->getFragment());
  }

  public function testSetFragment()
  {
    $fragment = new Fragment('test?param=value');
    self::assertSame('test?param=value', $fragment->getFragment());
    $fragment->setFragment('changed?param=value');
    self::assertSame('changed?param=value', $fragment->getFragment());
  }

  public function testGetSetPath()
  {
    $fragment = new Fragment();
    $path = new Path('test');
    $fragment->setPath($path);
    self::assertSame($path, $fragment->getPath());
    self::assertSame('test', (string)$fragment);
  }

  public function testGetSetQuery()
  {
    $fragment = new Fragment();
    $query = new Query('param=value');
    $fragment->setQuery($query);
    self::assertSame($query, $fragment->getQuery());
    self::assertSame('?param=value', (string)$fragment);
  }

  public function testToString()
  {
    $fragment = new Fragment('test?param=value');
    self::assertSame('test?param=value', (string)$fragment);
  }
}
