<?php

namespace UnitTest;

class Error
{
    public static int $counter;

    public static function exception(string $message): void
    {
        Handle::exception(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3), $message);
    }

    public static function message(string $message): void
    {
        self::$counter++;
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        Console::message(
            $trace[1]['line'],
            $trace[2]['class'] . $trace[2]['type'] . $trace[2]['function'],
            $trace[1]['function'],
            $message);
    }
}
