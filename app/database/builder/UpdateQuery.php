<?php

namespace app\database\builder;

class UpdateQuery
{
    private string $table;
    private array $fieldsAndValues;
    private array $where = [];
    private array $binds = [];
    public static function table(string $table): self
    {
        $self = new self();
        $self->table = $table;
        return $self;
    }
    public function set(array $fieldsAndValues): self
    {
        $this->fieldsAndValues = $fieldsAndValues;
        return $this;
    }
    public function where(string $field, string $operator, string | int | float $value, ?string $logic): self
    {
        $placeholder = '';
        $placeholder = $field;
        if (str_contains($placeholder, '.')) {
            $placeholder = substr($field, strpos($field, '.') + 1);
        }
        $this->where[] = "{$field} {$operator} :{$placeholder} {$logic}";
        $this->binds[$placeholder] = $value;
        return $this;
    }
    private function createQuery(): string
    {
        if (!$this->table) {
            throw new \Exception("Por favor informe o nome da tabela!");
        }
        if (!$this->fieldsAndValues) {
            throw new \Exception("Para atualizar informe os dados!");
        }
        $query = '';
        $query = "update {$this->table} set ";
        foreach ($this->fieldsAndValues as $field => $value) {
            $query .= "{$field} = :{$field}, ";
            $this->binds[$field] = $value;
        }
        $query = rtrim($query, ',');
        $query .= (isset($this->where) and (count($this->where) > 0))
            ? 
            ' where ' . implode(' ', $this->where)
            : '';
        return $query;
    }
}