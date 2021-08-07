<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

use App\Artia\Token\Token;
use Pecee\SimpleRouter\SimpleRouter;
use Pecee\SimpleRouter\Event\EventArgument;
use Pecee\SimpleRouter\Handlers\EventHandler;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\Http\Middleware\Exceptions\TokenMismatchException;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Exception\InvalidFileException;

try {
    $env = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $env->load();

    if (!isset($_SERVER['REQUEST_METHOD'])) {
        Token::revalited();
        exit();
    }

    $basePath = $_ENV['CONFIG_APP_PATH'];

    $eventHandler = new EventHandler();
    $eventHandler->register(EventHandler::EVENT_ADD_ROUTE, function (EventArgument $event) use ($basePath) {
        if (!$event->isSubRoute) {
            switch (true) {
                case $event->route instanceof \Pecee\SimpleRouter\Route\ILoadableRoute:
                    $event->route->prependUrl($basePath);
                    break;
                case $event->route instanceof \Pecee\SimpleRouter\Route\IGroupRoute:
                    $event->route->prependPrefix($basePath);
                    break;
            }
        }
    });

    SimpleRouter::addEventHandler($eventHandler);
    SimpleRouter::csrfVerifier(new \App\Http\Middleware\CsrfVerifier());
    SimpleRouter::setDefaultNamespace('\App\Http\Controllers');


    require __DIR__ . '/../routers/web.php';

    Token::loadCache();
    SimpleRouter::start();
} catch (InvalidPathException | InvalidFileException | NotFoundHttpException | TokenMismatchException $exception) {
    echo $exception->getMessage();
    exit();
}
