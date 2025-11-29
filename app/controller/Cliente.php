<?php

namespace app\controller;

use app\database\builder\SelectQuery;
use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;

class Cliente extends Base
{

    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de Cliente'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listacliente'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        try {
            $nome = $_POST['nome'];
            $sobrenome = $_POST['sobrenome'];
            $cpf = $_POST['cpf'];
            $rg = $_POST['rg'];
            $FieldsAndValues = [
                'nome_fantasia' => $nome,
                'sobrenome_razao' => $sobrenome,
                'cpf_cnpj' => $cpf,
                'rg_ie' => $rg
            ];
            if (is_null($nome) || $nome === '') {
                echo json_encode(['status' => false, 'msg' => 'Por favor informe o nome!', 'id' => 0]);
                die;
            }
            if (is_null($sobrenome) ||  $sobrenome === '') {
                echo json_encode(['status' => false, 'msg' => 'Por favor informe o sobrenome!', 'id' => 0]);
                die;
            }
            if (is_null($cpf) || $cpf === '') {
                echo json_encode(['status' => false, 'msg' => 'Por favor informe o cpf!', 'id' => 0]);
                die;
            }
            if (is_null($rg) || $rg === '') {
                echo json_encode(['status' => false, 'msg' => 'Por favor informe o rg!', 'id' => 0]);
                die;
            }
            $IsSave = InsertQuery::table('cliente')->save($FieldsAndValues);

            if (!$IsSave) {
                echo json_encode(['status' => false, 'msg' => $IsSave, 'id' => 0]);
                die;
            }
            echo json_encode(['status' => true, 'msg' => 'Salvo com sucesso!', 'id' => 0]);
            die;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de Cliente'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('cliente'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function listacliente($request, $response)
    {
        #Captura todas a variaveis de forma mais segura VARIAVEIS POST.
        $form = $request->getParsedBody();
        #Qual a coluna da tabela deve ser ordenada.
        $order = $form['order'][0]['column'];
        #Tipo de ordenação
        $orderType = $form['order'][0]['dir'];
        #Em qual registro se inicia o retorno dos registro, OFFSET
        $start = $form['start'];
        #Limite de registro a serem retornados do banco de dados LIMIT
        $length = $form['length'];
        $fields = [
            0 => 'id',
            1 => 'nome_fantasia',
            2 => 'sobrenome_razao',
            3 => 'cpf_cnpj',
            4 => 'rg_ie'
        ];
        #Capturamos o nome do capo a ser ordenado.
        $orderField = $fields[$order];
        #O termo pesquisado
        $term = $form['search']['value'];
        $query = SelectQuery::select('id,nome_fantasia,sobrenome_razao,cpf_cnpj,rg_ie')->from('cliente');
        if (!is_null($term) && ($term !== '')) {
            $query->where('cliente.nome_fantasia', 'ilike', "%{$term}%", 'or')
                ->where('cliente.sobrenome_razao', 'ilike', "%{$term}%", 'or')
                ->where('cliente.cpf_cnpj', 'ilike', "%{$term}%", 'or')
                ->where('cliente.rg_ie', 'ilike', "%{$term}%");
        }
        if (!is_null($order) && ($order !== '')) {
            $query->order($orderField, $orderType);
        }
        $clientes = $query
            ->limit($length, $start)
            ->fetchAll();
        $clienteData = [];
        foreach ($clientes as $key => $value) {
            $clienteData[$key] = [
                $value['id'],
                $value['nome_fantasia'],
                $value['sobrenome_razao'],
                $value['cpf_cnpj'],
                $value['rg_ie'],
                "<button type= 'button' onclick='Editar(". $value['id'] . ");' class='btn btn-warning'>Editar</button>
                <button type='button'  onclick='Delete(" . $value['id'] . ");' class='btn btn-danger'>Excluir</button>"
            ];
        }
        $data = [
            'status' => true,
            'recordsTotal' => count($clientes),
            'recordsFiltered' => count($clientes),
            'data' => $clienteData
        ];
        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    public function delete($request, $response)
    {
        try {
            $id = $_POST['id'];
            $IsDelete = DeleteQuery::table('cliente')
                ->where('id', '=', $id)
                ->delete();

            if (!$IsDelete) {
                echo json_encode(['status' => false, 'msg' => $IsDelete, 'id' => $id]);
                die;
            }
            echo json_encode(['status' => true, 'msg' => 'Removido com sucesso!', 'id' => $id]);
            die;
        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
        }
    }
}