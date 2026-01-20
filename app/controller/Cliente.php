<?php

namespace app\controller;

use app\database\builder\SelectQuery;
use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;
use app\database\builder\UpdateQuery;

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
            #Captura os dados do form
            $form = $request->getParsedBody();
            #Capturar os dados do usuário.
            $dadosCliente = [
                'nome_fantasia' => $form['nome_fantasia'],
                'sobrenome_razao' => $form['sobrenome_razao'],
                'cpf_cnpj' => $form['cpf_cnpj'],
                'rg_ie' => $form['rg_ie'],
                'senha' => password_hash($form['senhaCadastro'], PASSWORD_DEFAULT)
            ];
            $IsInseted = InsertQuery::table('cliente')->save($dadosCliente);
            if (!$IsInseted) {
                return $this->SendJson(
                    $response,
                    ['status' => false, 'msg' => 'Restrição: ' . $IsInseted, 'id' => 0],
                    403
                );
            }
            #Captura o código do ultimo usuário cadastrado na tabela de usuário
            $id = SelectQuery::select('id')->from('cliente')->order('id', 'desc')->fetch();
            #Colocamos o ID do ultimo usuário cadastrado na varaivel $id_cliente.
            $id_cliente = $id['id'];
            #Inserimos o e-mail
            $dadosContato = [
                'id_cliente' => $id_cliente,
                'tipo' => 'email',
                'contato' => $form['email']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            $dadosContato = [];
            #Inserimos o celular
            $dadosContato = [
                'id_cliente' => $id_cliente,
                'tipo' => 'celular',
                'contato' => $form['celular']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            $dadosContato = [];
            #Inserimos o WhastaApp
            $dadosContato = [
                'id_cliente' => $id_cliente,
                'tipo' => 'whatsapp',
                'contato' => $form['whatsapp']
            ];
            InsertQuery::table('contato')->save($dadosContato);
            return $this->SendJson($response, ['status' => true, 'msg' => 'Cadastro realizado com sucesso!', 'id' => $id_cliente], 201);
        } catch (\Exception $e) {
            return $this->SendJson($response, ['status' => true, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }

    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'acao' => 'c',
            'titulo' => 'Cadastro de Cliente'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('cliente'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function listacliente($request, $response)
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
            1 => 'nome_fantasia',
            2 => 'sobrenome_razao',
            3 => 'cpf_cnpj',
            4 => 'rg_ie',
            5 => 'email',
            6 => 'celular',
            7 => 'whatsapp'
        ];

        # Coluna escolhida
        $orderField = (intval($order) > 8) ? $fields[$order] : $fields[0];

        # Termo pesquisado
        $term = $form['search']['value'];

        # Agora a busca é feita na VIEW
        $query = SelectQuery::select()->from('vw_cliente_contatos');
        # Filtros de pesquisa
        if (!empty($term)) {
            $query->where('nome_fantasia', 'ilike', "%{$term}%", 'or')
                ->where('sobrenome_razao', 'ilike', "%{$term}%", 'or')
                ->where('cpf_cnpj', 'ilike', "%{$term}%", 'or')
                ->where('rg_ie', 'ilike', "%{$term}%", 'or')
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
                $value['nome_fantasia'],
                $value['sobrenome_razao'],
                $value['cpf_cnpj'],
                $value['rg_ie'] ?? '',      // deixa em branco se NULL
                $value['email'] ?? '',    // deixa em branco se NULL
                $value['celular'] ?? '',    // deixa em branco se NULL
                $value['whatsapp'] ?? '',   // deixa em branco se NULL
                "<a href='/cliente/alterar/{$value['id']}' class='btn btn-warning'>Editar</a>
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
    public function alterar($request, $response, $args)
    {
        $id = $args['id'];
        $cliente = SelectQuery::select()
            ->from('vw_cliente_contatos')
            ->where('id', '=', $id)
            ->fetch();

        $dadosTemplate = [
            'titulo' => 'Alterar Cliente',
            'cliente' => $cliente,
            'id' => $id,
            'acao' => 'alterar'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('cliente'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
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
                'nome_fantasia' => $form['nome_fantasia'],
                'sobrenome_razao' => $form['sobrenome_razao'],
                'cpf_cnpj' => $form['cpf_cnpj'],
                'rg_ie' => $form['rg_ie']
            ];
            $IsUpdate = UpdateQuery::table('cliente')->set($FieldAndValues)->where('id', '=', $id)->update();
            if (!$IsUpdate) {
                return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $IsUpdate, 'id' => 0], 403);
            }
            return $this->SendJson($response, ['status' => true, 'msg' => 'Atualizado com sucesso!', 'id' => $id]);
        } catch (\Exception $e) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
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
