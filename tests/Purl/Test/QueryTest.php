<?php

namespace Purl\Test;

use PHPUnit\Framework\TestCase;
use Purl\Query;

/**
 * Class QueryTest
 *
 * @package Purl\Test
 */
class QueryTest extends TestCase
{
  public function testConstruct()
  {
    $query = new Query('param=value');
    self::assertSame('param=value', $query->getQuery());
  }

  public function testGetSetQuery()
  {
    $query = new Query();
    self::assertSame('', $query->getQuery());
    $query->setQuery('param1=value1&param2=value2');
    self::assertSame('param1=value1&param2=value2', $query->getQuery());
  }

  public function testToString()
  {
    $query = new Query('param1=value1&param2=value2');
    self::assertSame('param1=value1&param2=value2', (string)$query);
  }

  public function testGetSetData()
  {
    $query = new Query('param1=value1&param2=value2');
    self::assertSame(['param1' => 'value1', 'param2' => 'value2'], $query->getData());
    $query->setData(['param' => 'value']);
    self::assertSame('param=value', $query->getQuery());
  }
}
