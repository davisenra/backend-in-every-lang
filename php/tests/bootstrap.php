<?php

use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

register_shutdown_function(function () {
    // shut down ReactPHP Event Loop, otherwise it'll run until the end of times
    Loop::stop();
});
