<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;

class Cliente extends Base
{
    public function lista($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Página inicial'
            ];
            return $this->getTwig()
                ->render($response, $this->setView('listacliente'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
        }
    }
    public function cadastro($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Página inicial'
            ];
            return $this->getTwig()
                ->render($response, $this->setView('cadcliente'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
        }
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

            $IsSave = InsertQuery::table('cliente')->save($FieldsAndValues);
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
            $IsDelete = DeleteQuery::table('cliente')
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
}