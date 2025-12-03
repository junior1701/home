<?php

namespace app\trait;

use Slim\Views\Twig;

trait Template
{
    public function getTwig()
    {
        try {
            $twig = Twig::create(DIR_VIEW);
            $twig->getEnvironment()->addGlobal('EMPRESA', 'Calango&CIA');
            return $twig;
        } catch (\Exception $e) {
            throw new \Exception("Restrição: " . $e->getMessage());
        }
    }
    public function setView($name)
    {
        return $name . EXT_VIEW;
    }
    public function SendJson($respone, array $data, int $statusCode = 200)
    {
        # Converte o array PHP para um JSON
        $playload = json_encode($data);
        # Retorna a resposta em formato json
        $respone->getBody()->write($playload);
        return $respone
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }       
}
