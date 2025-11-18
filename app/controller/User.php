<?php

namespace app\controller;

use app\database\builder\DeleteQuery;
use app\database\builder\InsertQuery;

class User extends Base
{
    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de Usuários'
        ];

        return $this->getTwig()
            ->render($response, $this->setView('listuser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de Usuários'
        ];

        return $this->getTwig()
            ->render($response, $this->setView('caduser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        try {
            $nome = $_POST['nome'];
            $sobrenome = $_POST['sobrenome'];
            $senha = $_POST['senha'];
            $cpf = $_POST['cpf'];
            $rg = $_POST['rg'];


            $FieldsAndValues = [
                'nome' => $nome,
                'sobrenome' => $sobrenome,
                'senha' => $senha,
                'cpf' => $cpf,
                'rg' => $rg
            ];

            $IsSave = InsertQuery::table('usuario')->save($FieldsAndValues);
            if (!$IsSave) {
                echo 'Erro ao salvar';
                die;
            }
            echo "Salvo com sucesso!";
            die;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function delete($request, $response)
    {
        try {
            $id = $_POST['id'];
            $IsDelete = DeleteQuery::table('usuario')
                ->where('id', '=', $id)
                ->delete();
            if (!$IsDelete) {
                echo 'Erro ao deletar';
                die;
            }
            echo "Deletado com sucesso!";
            die;
        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
        }
    }
    public function listuser($request, $response)
    {
        $form = $request->getParsedBody();
          $data =[
            'status' => true,
            'data' => [
                [
                 1,
                 'Junior',
                 'Silva',
                 '000.000.000-00',
                 '00.000.000-0'
                 
                ],
                [
                 2,
                 'Ana',
                 'Oliveira',
                 '111.111.111-11',
                 '11.111.111-1'
                ],
                [
                 3,
                 'Carlos',
                 'Souza',
                 '222.222.222-22',
                 '22.222.222-2'
                ],
                [
                 4,
                 'Mariana',
                 'Pereira',
                 '333.333.333-33',
                 '33.333.333-3'
                ],
                [
                 5,
                 'Pedro',
                 'Almeida',
                 '444.444.444-44',
                 '44.444.444-4'
                ],
            ]
        ];
        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);

        var_dump($form);

        /*
        order[0][column]
        order[0][dir]
        order[0][name]
        start
        length
        search[value]
    }

    */
    }
}
