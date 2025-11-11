<?php

namespace app\controller;

class User extends Base
{
    public function lista($request, $response, )
    {
        $dadosTemplate = [
            'titulo' => 'Lista de Usuários'
        ];

        return $this->getTwig()
            ->render($response, $this->setView('listuser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response, )
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de Usuários'
        ];

        return $this->getTwig()
            ->render($response, $this->setView('caduser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
}

