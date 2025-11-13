<?php

use app\controller\Cliente;
use app\controller\User;
use app\controller\Home;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home');

$app->get('/home', Home::class . ':home');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista');
    $group->get('/cadastro', User::class . ':cadastro');
    $group->post('/insert', User::class . ':insert');
    $group->post('/delete', User::class . ':delete');
});
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/lista', Cliente::class . ':lista');
    $group->get('/cadastro', Cliente::class . ':cadastro');
    $group->post('/insert', Cliente::class . ':insert');
    $group->post('/delete', Cliente::class . ':delete');
});