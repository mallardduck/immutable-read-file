<?php

use MallardDuck\ImmutaFopen\ImmutaFopen;

require __DIR__ . '/vendor/autoload.php';

$fileName = __DIR__ . '/tests/stubs/json.txt';

$step1 = ImmutaFopen::fromFilePath($fileName);
echo $step1->fgetc() . PHP_EOL; // The first character of your file
echo $step1->fgetc() . PHP_EOL; // The first character of your file, again
$step2 = $step1->advanceBytePosition(); // A new r/o immutable entity advanced a single byte by default.
echo $step2->fgetc() . PHP_EOL; // The second character of your file
echo $step2->fgetc() . PHP_EOL; // The second character of your file, again

$step1 = new SplFileObject($fileName, 'r');
echo $step1->fgetc() . PHP_EOL; // The first character of your file
echo $step1->fgetc() . PHP_EOL; // The second character of your file
$step2 = new SplFileObject($fileName, 'r');
$step2->fseek(1);
echo $step2->fgetc() . PHP_EOL; // The second character of your file
echo $step2->fgetc() . PHP_EOL; // The third character of your file
