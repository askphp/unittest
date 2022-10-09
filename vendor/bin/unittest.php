<?php

use UnitTest\Exception\UnittestException;
use UnitTest\Tests;

require __DIR__ . '/../autoload.php';

try {
    Tests::run(__DIR__ . '/../../tests');
} catch (UnittestException $e) {
    echo $e->getMessage();
    exit();
}
