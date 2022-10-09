<?php

namespace UnitTest;

class Console
{
    public static function header(): void
    {
        echo sprintf(
            'Running %sUnitTest%s: %s',
            "\033[34m", "\033[0m", date("M d Y H:i:s") . PHP_EOL . PHP_EOL
        );
    }

    public static function message(string $line, string $class, string $assert, string $message): void
    {
        echo sprintf(
            ':% 4s %s() > %s%s',
            $line, $class, $assert, ($message ? ' Message: ' . $message : $message) . PHP_EOL . PHP_EOL
        );
    }

    public static function exception(string $message, array $trace): void
    {
        $class = $trace['class'] . $trace['type'] . $trace['function'];
        echo sprintf(
            '%4$sException thrown%5$s: %s%4$sFrom%5$s: %s()%s',
            $message . PHP_EOL, $class, PHP_EOL . PHP_EOL, "\033[31m", "\033[0m"
        );
    }

    public static function ok(): void
    {
        echo sprintf(
            '%sOk!%s',
            "\033[32m", "\033[0m" . PHP_EOL . PHP_EOL
        );
    }

    public static function exceptions(): void
    {
        echo sprintf(
            '%sThrown exceptions in tests%s:%s',
            "\033[33m", "\033[0m", PHP_EOL
        );
    }

    public static function assertions(): void
    {
        echo sprintf(
            '%sTests without assertions%s:%s',
            "\033[33m", "\033[0m", PHP_EOL
        );
    }

    public static function list(string $class, string $method): void
    {
        echo sprintf(
            '- %s->%s()%s',
            $class, "\033[33m" . $method . "\033[0m", PHP_EOL
        );
    }

    public static function results(): void
    {
        echo sprintf(
            'Produced Tests %s, Assertions %s%s',
            "\033[32m" . Handle::$counter . "\033[0m", "\033[32m" . Assert::$counter . "\033[0m", (
            Error::$counter ? ', Errors ' . "\033[31m" . Error::$counter . "\033[0m" : ''
            ) . '.' . PHP_EOL
        );
    }

    public static function completed(): void
    {
        echo sprintf(
            'Testing completed in %s sec.',
            "\033[32m" . bcmod(microtime(true) - Handle::$timer, 1, 3) . "\033[0m"
        );
    }
}
