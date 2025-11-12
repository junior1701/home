<?php 

namespace App\Database\Builder;

class DeleteQuery
{
    private string $table;
    private array $where = [];
    private array $binds = [];
    public function table(string $table): self
    {
        $self = new self;
        $self->table = $table;
        return $self;
    }
}