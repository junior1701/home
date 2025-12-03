<?php

namespace app\controller;

use app\database\builder\InsertQuery;

class Login extends Base
{
    public function login($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Autenticação'
            ];
            return $this->getTwig()
                ->render($response, $this->setView('login'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
        }
    }
        
    public function precadastro($request, $response)
    {
        try 
        {
            #Captura os dados do formulário
            $form = $request->getParsedBody();
            #Captura os dados do usuário
            $dadosUsuario = [
                'nome'              => $form['nome'],
                'sobrenome'         => $form['sobrenome'],
                'cpf'               => $form['cpf'],
                'rg'                => $form['rg'],
                'senha'             => password_hash($form['senha'], PASSWORD_DEFAULT)
            ];
            $IsInseted = InsertQuery:: table('usuario')->save($dadosUsuario);
            if ($IsInseted) {
                return $this->SendJson(
                    $response, 
                    ['success' => true, 'msg' => "Restrição! : $IsInseted", 'id'=> 0],
                );
            } 
        }catch (\Exception $e) {

        }
        
    }
    public function autenticar($request, $response)
    {
        try {
        } catch (\Exception $e) {
        }
    }


}
