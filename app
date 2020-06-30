#!/usr/bin/env php
<?php

use Illuminate\{
	Console\Application,
	Container\Container,
	Events\Dispatcher
};
use Symfony\Component\Console\{
	Input\ArgvInput,
	Output\ConsoleOutput
};

try {
    require_once __DIR__.'/vendor/autoload.php';

    $container = new Container();
    $dispatcher = new Dispatcher();
    $app = new Application($container, $dispatcher, '0.2');
    $app->setName('Calculator');

    $commands = require_once __DIR__.'/commands.php';
    $commands = collect($commands)
        ->map(
            function ($command) use ($app) {
                return $app->getLaravel()->make($command);
            }
        )
        ->all()
    ;

    $app->addCommands($commands);

    $app->run(new ArgvInput(), new ConsoleOutput());
} catch (Throwable $e) {
	error_log($e->getMessage());
}
