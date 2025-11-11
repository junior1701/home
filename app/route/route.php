<?php

use app\controller\User;
use app\controller\Home;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home');

$app->get('/home', Home::class . ':home');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', user::class . ':lista');
    $group->get('/cadastro', user::class . ':cadastro');
});
