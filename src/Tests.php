<?php

namespace UnitTest;

use UnitTest\Exception\UnittestException;

class Tests
{
    private string $path;
    private array $tests;

    /**
     * @throws UnittestException
     */
    public function __construct(string $path)
    {
        if (!file_exists($path = realpath($path)))
            throw new UnittestException(sprintf(
                '%sThe path to the tests is not defined in %s',
                PHP_EOL, "\033[31m" . self::class . "\033[0m" . PHP_EOL . PHP_EOL
            ));
        Handle::$timer = microtime(true);
        $this->path = $path;
        $this->initialize($path . DIRECTORY_SEPARATOR . 'tests' . '.' . 'php');
    }

    /**
     * Running Tests.
     *
     * @param string $path path to tests.
     * @return static
     * @throws UnittestException
     */
    public static function run(string $path): static
    {
        return new static($path);
    }

    private function initialize(string $path): void
    {
        ($exist = file_exists($path)) ? $this->tests($path) : $this->recursive($this->path);
        if (isset($this->tests))
            $exist ?: $this->print($path, ...$this->tests);
        else
            $this->tests = [[], []];
        new Handle($this->tests);
    }

    private function tests(string $path): void
    {
        $tests = function (array $test): void {
            list($class, $method) = $test;
            $dir = pathinfo($class, PATHINFO_DIRNAME);
            isset($this->tests['directories'][$dir]) ?: $this->tests['directories'][$dir][] = $class;
            in_array($class, $this->tests['directories'][$dir]) ?: $this->tests['directories'][$dir][] = $class;
            $this->tests['tests'][$class][] = $method;
        };
        $method = function (array $test) use ($tests): void {
            if (str_starts_with($test[1], 'test'))
                $tests($test);
        };
        $directories = function (array $test) use ($method): void {
            if (str_ends_with($test[0], 'Test'))
                $method($test);
        };
        foreach (require $path as $test)
            if (isset($test[1]))
                $directories($test);
    }

    private function recursive(string $path): void
    {
        $tests = function (string $class, array $methods) {
            $this->tests['directories'][pathinfo($class, PATHINFO_DIRNAME)][] = $class;
            foreach ($methods as $method)
                if (str_starts_with($method, 'test'))
                    $this->tests['tests'][$class][] = $method;
        };
        $method = function (string $class) use ($tests): void {
            if ($methods = get_class_methods($class))
                $tests($class, $methods);
        };
        $directories = function (string $path) use ($method): void {
            if (str_ends_with($path, 'Test.php')) {
                $method('Test' . substr($path, strlen($this->path), -4));
            }
        };
        $separator = function (string $path) use ($directories): void {
            is_file($path) ? $directories($path) : $this->recursive($path);
        };
        if ($items = scandir($path))
            foreach ($items as $item)
                if (in_array($item, ['.', '..']))
                    continue;
                else
                    $separator($path . DIRECTORY_SEPARATOR . $item);
    }

    private function print(string $path, array $directories, array $tests): void
    {
        $print = sprintf(
            '%s?php%snamespace Test;%sreturn [%s',
            '<', PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, PHP_EOL
        );
        foreach ($directories as $directory)
            foreach ($directory as $class)
                foreach ($tests[$class] ?? [] as $method)
                    $print .= '    [' . substr($class, 5) . '::class, ' . "'" . $method . "']," . PHP_EOL;
        $print .= '];' . PHP_EOL;
        file_put_contents($path, $print);
    }
}
