<?php

namespace app\middleware;

use app\database\builder\SelectQuery;

class Middleware
{
    public static function authentication()
    {
        $middleware = function ($request, $handler) {
            $response = $handler->handle($request);
            $method = $request->getMethod();
            $pagina = $request->getRequestTarget();
            if ($method === 'GET') {
                $usuarioLogado = empty($_SESSION['usuario']) || empty($_SESSION['usuario']['logado']);
                if ($usuarioLogado && $pagina !== '/login') {
                    session_destroy();
                    return $response->withHeader('Location', '/login')->withStatus(302);
                }
                if (!$usuarioLogado && $pagina === '/login') {
                    return $response->withHeader('Location', '/')->withStatus(302);
                }
                if ($pagina === '/login') {
                    if (!$usuarioLogado) {
                        return $response->withHeader('Location', '/')->withStatus(302);
                    }
                }
                if (empty($_SESSION['usuario']['ativo']) or !$_SESSION['usuario']['ativo']) {
                    session_destroy();
                    return $response->withHeader('Location', '/login')->withStatus(302);
                }
            }
            return $handler->handle($request);
        };
        return $middleware;
    }
}