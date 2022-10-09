<?php

namespace UnitTest;

class Assert
{
    public static int $counter;

    protected static function assertException(string $message = ''): void
    {
        self::$counter++;
        Error::exception($message);
    }

    protected static function assertEquals($expected, $actual, string $message = ''): void
    {
        self::$counter++;
        $expected === $actual ?: Error::message($message);
    }
}
