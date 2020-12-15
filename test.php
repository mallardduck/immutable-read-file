<?php

use MallardDuck\ImmutaFopen\ImmutaFopen;

require __DIR__ . '/vendor/autoload.php';


$socket = ImmutaFopen::fromFilePath(__DIR__ . '/tests/stubs/json.txt');

dd(
    $socket,
    $socket->fgetc(),
    $socket->fgetc(),
    $socket,
    $socket->fread(5),
    (string) $socket,
    $socket->fgetc(),
    (string) $socket,
    $socket->fread(5),
);