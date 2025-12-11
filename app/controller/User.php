<?php

namespace app\controller;

use app\database\builder\SelectQuery;
use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;

class User extends Base
{

    public function lista($request, $response)
    {

        $dadosTemplate = [
            'titulo' => 'Lista de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listauser'), $dadosTemplate)
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
                'nome' => $nome,
                'sobrenome' => $sobrenome,
                'cpf' => $cpf,
                'rg' => $rg
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
            $IsSave = InsertQuery::table('usuario')->save($FieldsAndValues);

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
            'titulo' => 'Cadastro de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('user'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function listauser($request, $response)
    {
        # Captura todas as variáveis de forma segura
        $form = $request->getParsedBody();

        # Ordenação
        $order = $form['order'][0]['column'];
        $orderType = $form['order'][0]['dir'];

        # Paginação
        $start = $form['start'];
        $length = $form['length'];

        # Mapeamento de colunas para ordenação
        $fields = [
            0 => 'id',
            1 => 'nome',
            2 => 'sobrenome',
            3 => 'cpf',
            4 => 'email',
            5 => 'celular',
            6 => 'whatsapp'
        ];

        # Coluna escolhida
        $orderField = $fields[$order];

        # Termo pesquisado
        $term = $form['search']['value'];

        # Agora a busca é feita na VIEW
        $query = SelectQuery::select('*')->from('vw_usuario_contatos');

        # Filtros de pesquisa
        if (!empty($term)) {

            $query->where('nome', 'ilike', "%{$term}%", 'or')
                ->where('sobrenome', 'ilike', "%{$term}%", 'or')
                ->where('cpf', 'ilike', "%{$term}%", 'or')
                ->where('email', 'ilike', "%{$term}%", 'or')
                ->where('celular', 'ilike', "%{$term}%", 'or')
                ->where('whatsapp', 'ilike', "%{$term}%");
        }

        # Ordenação dinâmica
        if (!empty($order)) {
            $query->order($orderField, $orderType);
        }

        # Paginação
        $users = $query
            ->limit($length, $start)
            ->fetchAll();

        # Montagem dos dados
        $userData = [];
        foreach ($users as $key => $value) {
            $userData[$key] = [
                $value['id'],
                $value['nome'],
                $value['sobrenome'],
                $value['cpf'],
                $value['email'] ?? '',      // deixa em branco se NULL
                $value['celular'] ?? '',    // deixa em branco se NULL
                $value['whatsapp'] ?? '',   // deixa em branco se NULL
                "<button class='btn btn-warning'>Editar</button>
         <button type='button' onclick='Delete(" . $value['id'] . ");' class='btn btn-danger'>Excluir</button>"
            ];
        }


        # Resposta final
        $data = [
            'status' => true,
            'recordsTotal' => count($users),
            'recordsFiltered' => count($users),
            'data' => $userData
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
            $IsDelete = DeleteQuery::table('usuario')
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
