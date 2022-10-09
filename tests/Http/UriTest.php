<?php

namespace Test\Http;

use AskPHP\Http\Uri;
use UnitTest\TestCase;

class UriTest extends TestCase
{
    private static Uri $uri;

    public static function setUpBeforeClass(): void
    {
//        new Example(); // Для проверки выброса исключения из vendor_autoload()
        self::$uri = new Uri(Test::URI);
    }

    public function testUri()
    {
        self::assertException();
        new Uri('https://');
    }

    public function testScheme()
    {
        self::assertEquals('http', self::$uri->scheme());
        $uri = new Uri('path');
        self::assertEquals('', $uri->scheme());
        self::assertException();
        new Uri('error://host');
    }

    public function testHost()
    {
        self::assertEquals('host.loc', self::$uri->host());
        $uri = new Uri('path');
        self::assertEquals('', $uri->host());
    }

    public function testPath()
    {
        self::assertEquals('/path', self::$uri->path());
        $uri = new Uri('path');
        self::assertEquals('/path', $uri->path());
        $uri = new Uri('https://host');
        self::assertEquals('/', $uri->path());
    }

    public function testQuery()
    {
        self::assertEquals('?query=> <', self::$uri->query());
        $uri = new Uri('https://host');
        self::assertEquals('', $uri->query());
    }

    public function testFragment()
    {
        self::assertEquals('#anchor', self::$uri->fragment());
        $uri = new Uri('https://host');
        self::assertEquals('', $uri->fragment());
    }
}
