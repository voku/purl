<?php

namespace Purl\Test;

use PHPUnit\Framework\TestCase;
use Purl\Url;

/**
 * Class PurlTest
 *
 * @package Purl\Test
 */
class PurlTest extends TestCase
{
  public function testMemoryConsumption()
  {
    $domains = [
        'http://google.de',
        'http://google.com',
        'http://google.it',
        'https://google.de',
        'https://google.com',
        'https://google.it',
        'http://www.google.de',
        'http://www.google.com',
        'http://www.google.it',
    ];

    $memStart = memory_get_usage(true);
    foreach ($domains as $key => $domain) {
      $purl[$key] = Url::parse($domain);
      self::assertInstanceOf('Purl\Url', $purl[$key]);
    }
    $memEnd = memory_get_usage(true);

    self::assertSame($this->roundMemoryUsage($memStart), $this->roundMemoryUsage($memEnd));
  }

  /**
   * @param $size
   *
   * @return string
   */
  protected function roundMemoryUsage($size)
  {
    return round($size / pow(1024, $i = floor(log($size, 1024))));
  }

}
