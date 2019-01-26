<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
use dbeurive\Log\Logger;

// Output example:
//
// 20190126110146 5c4c300a24ec9 FATAL R This is a fatal error
// 20190126110146 5c4c300a24ec9 ERROR R This is a standard error
// 20190126110146 5c4c300a24ec9 WARNING R This is a warning
// 20190126110146 5c4c300a24ec9 INFO R This is a informative message
// 20190126110146 5c4c300a24ec9 INFO L This%20is%20a%20multiline%20informative%20message%0Aother%20line.

$logger = new Logger(__DIR__ . DIRECTORY_SEPARATOR . 'log-info.log',
    Logger::LEVEL_INFO);

$logger->fatal("This is a fatal error");
$logger->error("This is a standard error");
$logger->warning("This is a warning");
$logger->info("This is a informative message");
$logger->info("This is a multiline informative message\nother line.");
$logger->data("This is a data"); // Not written
$logger->data(array('a' => 1)); // Not written
$logger->debug('This is a debug'); // Not written

// Output example:
//
// 20190126110146 5c4c300a281be FATAL R This is a fatal error
// 20190126110146 5c4c300a281be ERROR R This is a standard error
// 20190126110146 5c4c300a281be WARNING R This is a warning
// 20190126110146 5c4c300a281be INFO R This is a informative message
// 20190126110146 5c4c300a281be INFO L This%20is%20a%20multiline%20informative%20message%0Aother%20line.
// 20190126110146 5c4c300a281be DATA R This is a data
// 20190126110146 5c4c300a281be DATA L Array%0A%28%0A%20%20%20%20%5Ba%5D%20%3D%3E%201%0A%29%0A

$logger = new Logger(__DIR__ . DIRECTORY_SEPARATOR . 'log-data.log',
    Logger::LEVEL_DATA);

$logger->fatal("This is a fatal error");
$logger->error("This is a standard error");
$logger->warning("This is a warning");
$logger->info("This is a informative message");
$logger->info("This is a multiline informative message\nother line.");
$logger->data("This is a data");
$logger->data(array('a' => 1));
$logger->debug('This is a debug'); // Not written

// Output example:
//
// 20190126111327 123 FATAL R This is a fatal error
// 20190126111327 123 ERROR R This is a standard error
// 20190126111327 123 WARNING R This is a warning
// 20190126111327 123 INFO R This is a informative message
// 20190126111327 123 INFO L This%20is%20a%20multiline%20informative%20message%0Aother%20line.

$logger = new Logger(__DIR__ . DIRECTORY_SEPARATOR . 'log-info-session.log',
    Logger::LEVEL_INFO,
    '123');

$logger->fatal("This is a fatal error");
$logger->error("This is a standard error");
$logger->warning("This is a warning");
$logger->info("This is a informative message");
$logger->info("This is a multiline informative message\nother line.");
$logger->data("This is a data"); // Not written
$logger->data(array('a' => 1)); // Not written
$logger->debug('This is a debug'); // Not written

