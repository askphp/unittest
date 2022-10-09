<?php

namespace UnitTest;

use Exception;

class Handle
{
    public static float $timer;
    public static int $counter;

    private static array $exception;
    private array $exceptions;
    private array $assertions;
    private bool $throw;

    public function __construct(array $tests)
    {
        Assert::$counter = 0;
        Error::$counter = 0;
        self::$counter = 0;
        self::$exception = [];
        $this->throw = true;
        $this->console(...$tests);
    }

    public static function exception(array $trace, string $message): void
    {
        self::$exception = [
            'file' => $trace[1]['file'],
            'message' => [
                'line' => $trace[1]['line'],
                'class' => $trace[2]['class'] . $trace[2]['type'] . $trace[2]['function'],
                'assert' => $trace[1]['function'],
                'message' => $message,
            ],
        ];
    }

    private function console(array $directories, array $tests): void
    {
        Console::header();
        foreach ($directories as $directory)
            foreach ($directory as $class)
                $this->executor($class, $tests);
        if (0 === Error::$counter)
            Console::ok();
        if (isset($this->exceptions)) {
            Console::exceptions();
            foreach ($this->exceptions as $exception) {
                list($class, $method) = $exception;
                Console::list($class, $method);
            }
        }
        if (isset($this->assertions)) {
            Console::assertions();
            foreach ($this->assertions as $assertion) {
                list($class, $method) = $assertion;
                Console::list($class, $method);
            }
        }
        Console::results();
        Console::completed();

    }

    private function executor(string $class, array $tests): void
    {
        $assert = function (array $methods, object $new, string $method, int $assert) use ($class): void {
            try {
                !in_array('setUp', $methods) ?: $new->setUp();
                $new->$method();
                !in_array('tearDown', $methods) ?: $new->tearDown();
            } catch (Exception $e) {
                $this->message($e->getMessage(), $e->getTrace(), $class, $method);
            }
            $this->conditions($assert, $class, $method);
        };
        $methods = get_class_methods($class);
        $new = new $class();
        !in_array('setUpBeforeClass', $methods) ?: $new::setUpBeforeClass();
        foreach ($tests[$class] ?? [] as $method) {
            self::$counter++;
            $assert($methods, $new, $method, Assert::$counter);
        }
        !in_array('tearDownAfterClass', $methods) ?: $new::tearDownAfterClass();
    }

    private function message(string $message, array $trace, string $class, string $method): void
    {
        $exception = self::$exception;
        self::$exception = [];
        if ($exception) {
            if ($this->pass($trace, ...$exception))
                return;
            Error::$counter++;
            Console::message(...$exception['message']);
        } else {
            $this->throw = false;
            $this->exceptions[] = [$class, $method];
            Console::exception($message, $trace[0]);
        }
    }

    private function pass(array $trace, string $file, array $message): bool
    {
        foreach ($trace as $key => $item)
            if ($item['file'] === __DIR__ . DIRECTORY_SEPARATOR . 'Handle.php') {
                $trace = array_slice($trace, $key - 1, 2);
                break;
            }
        return ($file === $trace[0]['file'] and $message['line'] + 1 === $trace[0]['line']);
    }

    private function conditions(int $counter, string $class, string $method): void
    {
        if ($this->throw and Assert::$counter === $counter)
            $this->assertions[] = [$class, $method];
        if (self::$exception)
            if (self::$exception['message']['class'] === $class . '->' . $method) {
                Error::$counter++;
                Console::message(...self::$exception['message']);
            }
    }
}
