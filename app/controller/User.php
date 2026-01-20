<?php

namespace app\controller;

use app\database\builder\SelectQuery;
use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;
use app\database\builder\UpdateQuery;

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
            #Captura os dados do form
            $form = $request->getParsedBody();
            #Capturar os dados do usuário.
            $dadosUsuario = [
                'nome' => $form['nome'],
                'sobrenome' => $form['sobrenome'],
                'cpf' => $form['cpf'],
                'rg' => $form['rg'],
                'senha' => password_hash($form['senhaCadastro'], PASSWORD_DEFAULT)
            ];
            $IsInseted = InsertQuery::table('usuario')->save($dadosUsuario);
            if (!$IsInseted) {
                return $this->SendJson(
                    $response,
                    ['status' => false, 'msg' => 'Restrição: ' . $IsInseted, 'id' => 0],
                    403
                );
            }
            #Captura o código do ultimo usuário cadastrado na tabela de usuário
            $id = SelectQuery::select('id')->from('usuario')->order('id', 'desc')->fetch();
            #Colocamos o ID do ultimo usuário cadastrado na varaivel $id_usuario.
            $id_usuario = $id['id'];
            #Inserimos o e-mail
            $dadosContato = [
                'id_usuario' => $id_usuario,
                'tipo' => 'email',
                'contato' => $form['email']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            $dadosContato = [];
            #Inserimos o celular
            $dadosContato = [
                'id_usuario' => $id_usuario,
                'tipo' => 'celular',
                'contato' => $form['celular']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            $dadosContato = [];
            #Inserimos o WhastaApp
            $dadosContato = [
                'id_usuario' => $id_usuario,
                'tipo' => 'whatsapp',
                'contato' => $form['whatsapp']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            return $this->SendJson($response, ['status' => true, 'msg' => 'Cadastro realizado com sucesso!', 'id' => $id_usuario], 201);
        } catch (\Exception $e) {
            return $this->SendJson($response, ['status' => true, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'acao' => 'c',
            'titulo' => 'Cadastro de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('user'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function listuser($request, $response)
    {
        # Captura todas as variáveis de forma segura
        $form = $request->getParsedBody();

        # Ordenação
        $order = $form['order'][0]['column'] ?? 0;
        $orderType = $form['order'][0]['dir'] ?? 'asc';

        # Paginação
        $start = $form['start'] ?? 0;
        $length = $form['length'] ?? 10;

        # Mapeamento de colunas para ordenação
        $fields = [
            0 => 'id',
            1 => 'nome',
            2 => 'sobrenome',
            3 => 'cpf',
            6 => 'rg',
            4 => 'email',
            5 => 'celular',
            7 => 'whatsapp'
        ];

        # Coluna escolhida
        $orderField = (intval($order) > 8) ? $fields[$order] : $fields[0];

        # Termo pesquisado
        $term = $form['search']['value'];

        # Agora a busca é feita na VIEW
        $query = SelectQuery::select()->from('vw_usuario_contatos');

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
                $value['rg'],
                $value['email'] ?? '',      // deixa em branco se NULL
                $value['celular'] ?? '',    // deixa em branco se NULL
                $value['whatsapp'] ?? '',   // deixa em branco se NULL
                "<a href='/usuario/alterar/{$value['id']}' class='btn btn-warning'>Editar</a>
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
        return $this->SendJson($response, $data);
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
    public function alterar($request, $response, $args)
    {
        try {
            $id = $args['id'];
            $user = SelectQuery::select()->from('vw_usuario_contatos')->where('id', '=', $id)->fetch();
            $dadosTemplate = [
                'acao' => 'e',
                'id' => $id,
                'titulo' => 'Cadastro e edição',
                'user' => $user
            ];
            return $this->getTwig()
                ->render($response, $this->setView('user'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
    public function update($request, $response)
    {
        try {
            $form = $request->getParsedBody();
            $id = $form['id'];
            if (is_null($id) || empty($id)) {
                return $this->SendJson($response, ['status' => false, 'msg' => 'Por favor informe o ID', 'id' => 0], 500);
            }
            $FieldAndValues = [
                'nome' => $form['nome'],
                'sobrenome' => $form['sobrenome'],
                'cpf' => $form['cpf'],
                'rg' => $form['rg']
            ];
            $IsUpdate = UpdateQuery::table('usuario')->set($FieldAndValues)->where('id', '=', $id)->update();
            if (!$IsUpdate) {
                return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $IsUpdate, 'id' => 0], 403);
            }
            return $this->SendJson($response, ['status' => true, 'msg' => 'Atualizado com sucesso!', 'id' => $id]);
        } catch (\Exception $e) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }
}
