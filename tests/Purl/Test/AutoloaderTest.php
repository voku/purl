<?php

namespace Purl\Test;

use PHPUnit_Framework_TestCase;
use Purl\Autoloader;

/**
 * Class AutoloaderTest
 *
 * @package Purl\Test
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $loader = Autoloader::register();
        self::assertTrue(spl_autoload_unregister(array($loader, 'autoload')));
    }

    public function testAutoloader()
    {
        $loader = new Autoloader(__DIR__ . '/../../fixtures/autoloader');

        self::assertNull($loader->autoload('NonPurlClass'));
        self::assertFalse(class_exists('NonPurlClass'));

        $loader->autoload('Purl\Foo');
        self::assertTrue(class_exists('Purl\Foo'));

        // Test with a starting slash
        $loader->autoload('\Purl\Bar');
        self::assertTrue(class_exists('\Purl\Bar'));
    }
}
