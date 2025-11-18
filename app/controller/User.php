<?php

namespace app\controller;

use app\database\builder\DeleteQuery;
use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;

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
        # O índice da coluna para ordenação
        $order = $form['order'][0]['column'];
        # O tipo de ordenação (ascendente ou descendente)
        $orderType = $form['order'][0]['dir'];
        # O índice do primeiro registro da página
        $form['start'];
        # A quantidade de registros por página
        $form['length'];
        # O termo de pesquisa
        $form['search']['value'];
        # O termo pesquisado
        $term = $form['search']['value'];

        $query = SelectQuery::select('id, nome, sobrenome, cpf, rg')->from('usuario');

        if (!is_null($term) && ($term !== '')) {
            $query->where('nome', 'ilike', $term, 'or')
                  ->where('sobrenome', 'ilike', $term );

        }

        $users = $query->fetchAll();

        $userData = [];
        foreach ($users as $key => $value) {
            $usersData[$key] = [
                $value['id'],
                $value['nome'],
                $value['sobrenome'],
                $value['cpf'],
                $value['rg'],
                "<button class='btn btn-sm btn-warning'><i class='fa-solid fa-pen-to-square'></i>Editar</button>
                 <button class='btn btn-sm btn-danger btn-delete'><i class='fa-solid fa-trash'></i>Excluir</button>"
            ];
           
        }

        $data =[
            'status' => true,
            'recordsTotal' => count($users),
            'recordsFiltered' => count($users),
            'data' => $usersData
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
