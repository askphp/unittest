<?php

use UnitTest\Exception\UnittestException;

function vendor_autoload(string $class): string
{
    $file = function (int $offset) use ($class): string {
        return str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $offset)) . '.php';
    };
    /**
     * @throws UnittestException
     */
    $tests = function (string $file) use ($class): string {
        if (str_ends_with($file, 'Test.php'))
            return 'tests' . $file;
        throw new UnittestException(sprintf('Invalid class in test directory: %s.%s'
            . 'Allowed classes ending in - %4$s, examples: Test\%4$s, Test\Example%4$s.%s',
            "\033[31m" . $class . "\033[0m", PHP_EOL, PHP_EOL, "\033[32m" . 'Test' . "\033[0m"
        ));
    };
    return match (explode('\\', $class)[0]) {
        'AskPHP' => 'example' . $file(6),
        'Test' => $tests($file(4)),
        'UnitTest' => 'src' . $file(8),
    };
}

spl_autoload_register(function (string $class) {
    try {
        $class_path = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . vendor_autoload($class);
    } catch (UnittestException $e) {
        echo $e->getMessage();
        exit();
    }
    require $class_path;
});
