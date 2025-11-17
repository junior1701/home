<?php

namespace app\database\builder; // Define o namespace da classe

use app\database\Connection; // Importa a classe de conexão com o banco

class SelectQuery // Início da classe que monta SELECT
{
    private string $fields;   // Campos do SELECT
    private string $table;    // Nome da tabela
    private array $where = []; // Condições WHERE
    private array $binds = []; // Valores para bind
    private string $order;     // Parte ORDER BY
    private int $limit;        // Limite de registros
    private int $offset;       // Offset dos registros
    private string $limits;    // Parte LIMIT/OFFSET

    public static function select(string $fields = '*'): self // Método estático para iniciar SELECT
    {
        $self = new self;      // Cria instância da classe
        $self->fields = $fields; // Define campos do SELECT
        return $self;          // Retorna a instância
    }

    public function from(string $table): self // Define a tabela
    {
        $this->table = $table; // Armazena nome da tabela
        return $this;          // Retorna instância para encadeamento
    }

    public function where(string $field, string $operator, string|int $value, ?string $logic = null): self // Adiciona condição WHERE
    {
        $placeholder = '';     // Cria placeholder inicial
        $placeholder = $field; // Usa o campo como base do placeholder

        if (str_contains($placeholder, '.')) { // Se tiver alias (ex: t.nome)
            $placeholder = substr($field, strpos($field, '.') + 1); // Remove prefixo
        }

        $this->where[] = "{$field}  {$operator} :{$placeholder} {$logic}"; // Adiciona condição formatada
        $this->binds[$placeholder] = $value; // Guarda valor do bind
        return $this; // Retorna instância
    }

    public function order(string $field, string $typeOrder = 'asc'): self // Define ORDER BY
    {
        $this->order = " order by {$field}  {$typeOrder}"; // Monta ORDER BY
        return $this; // Encadeamento
    }

    public function limit(int $limit, int $offset = 0): self // Define LIMIT e OFFSET
    {
        $this->limit = $limit; // Armazena limite
        $this->offset = $offset; // Armazena offset
        $this->limits = " limit {$this->limit} offset {$this->offset} "; // Monta cláusula LIMIT
        return $this; // Encadeamento
    }

    private function createQuery(): string // Monta a query completa
    {
        if (!$this->fields) { // Verifica se campos foram definidos
            throw new \Exception("Para realizar uma consulta SQL é necessário informa os campos da consulta");
        }

        if (!$this->table) { // Verifica se a tabela foi definida
            throw new \Exception("Para realizar a consulta SQL é necessário informa a nome da tabela.");
        }

        $query = '';           // Inicia query vazia
        $query = 'select ';    // Inicia SELECT
        $query .= $this->fields . ' from '; // Adiciona campos e FROM
        $query .= $this->table; // Adiciona tabela
        $query .= (isset($this->where) and (count($this->where) > 0)) ? ' where ' . implode(' ', $this->where) : ''; // Monta WHERE se existir
        $query .= $this->order ?? ''; // Adiciona ORDER
        $query .= $this->limits ?? ''; // Adiciona LIMIT/OFFSET
        return $query; // Retorna SQL final
    }

    public function fetch() // Executa SELECT e retorna um registro
    {
        $query = '';           // Inicia query vazia
        $query = $this->createQuery(); // Monta query final

        try {
            $connection = Connection::connection(); // Abre conexão com banco
            $prepare = $connection->prepare($query); // Prepara query
            $prepare->execute($this->bind ?? []); // Executa com binds
            return $prepare->fetch(\PDO::FETCH_ASSOC); // Retorna linha única
        } catch (\Exception $e) { // Captura erros
            throw new \Exception("Restrição: " . $e->getMessage()); // Lança exceção personalizada
        }
    }

    public function fetchAll() // Executa SELECT e retorna todos registros
    {
        $query = '';           // Inicia query vazia
        $query = $this->createQuery(); // Monta query final

        try {
            $connection = Connection::connection(); // Abre conexão
            $prepare = $connection->prepare($query); // Prepara
            $prepare->execute($this->bind ?? []); // Executa com binds
            return $prepare->fetchAll(\PDO::FETCH_ASSOC); // Retorna todos
        } catch (\Exception $e) { // Captura erro
            throw new \Exception("Restrição: " . $e->getMessage()); // Exceção personalizada
        }
    }
}
