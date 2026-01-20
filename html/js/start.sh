#!/bin/bash

cd /home/junior/

rm -R vendor/
rm -R composer.lock

composer install --no-dev --no-progress -a
composer update --no-dev --no-progress -a
composer upgrade --no-dev --no-progress -a
composer dump-autoload -o

NOME_USUARIO="junior"
SENHA_USUARIO="junior"
NOME_BANCO="junior"

configurar_postgresql() {

    sudo -u postgres psql -c "DO \$\$
    BEGIN
        IF NOT EXISTS (
            SELECT FROM pg_roles WHERE rolname = '$NOME_USUARIO'
        ) THEN
            CREATE ROLE $NOME_USUARIO WITH
                LOGIN
                SUPERUSER
                CREATEDB
                CREATEROLE
                INHERIT
                REPLICATION
                PASSWORD '$SENHA_USUARIO';
        ELSE
            RAISE NOTICE 'Usuário já existe: $NOME_USUARIO';
        END IF;
    END
    \$\$;"

    sudo -u postgres psql -c "DO \$\$
    BEGIN
        IF NOT EXISTS (
            SELECT FROM pg_database WHERE datname = '$NOME_BANCO'
        ) THEN
            CREATE DATABASE $NOME_BANCO OWNER $NOME_USUARIO;
        ELSE
            RAISE NOTICE 'Banco já existe: $NOME_BANCO';
        END IF;
    END
    \$\$;"

}

criar_tabelas() {

    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE usuario (
        id BIGSERIAL PRIMARY KEY,
        nome TEXT,
        sobrenome TEXT,
        cpf TEXT,
        rg TEXT,
        senha TEXT,
        ativo BOOLEAN DEFAULT FALSE,
        administrador BOOLEAN DEFAULT FALSE,
        codigo_verificacao text,
        data_cadastro TIMESTAMP default CURRENT_TIMESTAMP ,
        data_alteracao TIMESTAMP default CURRENT_TIMESTAMP
    );
    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE cliente (
        id BIGSERIAL PRIMARY KEY,
        nome_fantasia TEXT,
        sobrenome_razao TEXT,
        cpf_cnpj TEXT,
        rg_ie TEXT,
        senha TEXT,
        ativo BOOLEAN DEFAULT FALSE,
        codigo_verificacao text,
        data_cadastro TIMESTAMP default CURRENT_TIMESTAMP ,
        data_alteracao TIMESTAMP default CURRENT_TIMESTAMP
    );
    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE fornecedor (
        id BIGSERIAL PRIMARY KEY,
        nome_fantasia TEXT,
        sobrenome_razao TEXT,
        cpf_cnpj TEXT,
        rg_ie TEXT,
        senha TEXT,
        ativo BOOLEAN DEFAULT FALSE,
        codigo_verificacao text,
        data_cadastro TIMESTAMP default CURRENT_TIMESTAMP ,
        data_alteracao TIMESTAMP default CURRENT_TIMESTAMP
    );
    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE empresa (
        id BIGSERIAL PRIMARY KEY,
        nome_fantasia TEXT,
        razao_social TEXT,
        cnpj TEXT,
        ie TEXT,
        senha TEXT,
        ativo BOOLEAN DEFAULT FALSE,
        codigo_verificacao text,
        data_cadastro TIMESTAMP default CURRENT_TIMESTAMP ,
        data_alteracao TIMESTAMP default CURRENT_TIMESTAMP
    );
    "


    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE contato (
        id BIGSERIAL PRIMARY KEY,
        id_usuario BIGINT REFERENCES usuario(id) on update cascade on delete cascade,
        id_cliente BIGINT REFERENCES cliente(id) on update cascade on delete cascade,
        id_fornecedor BIGINT REFERENCES fornecedor(id) on update cascade on delete cascade,
        id_empresa BIGINT REFERENCES empresa(id) on update cascade on delete cascade,
        tipo TEXT,
        contato TEXT,
        data_cadastro TIMESTAMP,
        data_alteracao TIMESTAMP
    );
    "

    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE OR REPLACE VIEW vw_cliente_contatos AS
    SELECT 
        cl.id,
        cl.nome_fantasia,
        cl.sobrenome_razao,
        cl.cpf_cnpj,
        cl.rg_ie,
        cl.senha,
        cl.ativo,
        cl.codigo_verificacao,
        MAX(CASE WHEN c.tipo = 'email' THEN c.contato END) AS email,
        MAX(CASE WHEN c.tipo = 'celular' THEN c.contato END) AS celular,
        MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato END) AS whatsapp,
        cl.data_cadastro,
        cl.data_alteracao
    FROM cliente cl
    LEFT JOIN contato c ON c.id_cliente = cl.id
    GROUP BY cl.id, cl.nome_fantasia, cl.sobrenome_razao, cl.cpf_cnpj, cl.rg_ie, cl.data_cadastro, cl.data_alteracao;

    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE OR REPLACE VIEW vw_fornecedor_contatos AS
    SELECT 
        f.id,
        f.nome_fantasia,
        f.sobrenome_razao,
        f.cpf_cnpj,
        f.rg_ie,
        f.senha,
        f.ativo,
        f.codigo_verificacao,
        MAX(CASE WHEN c.tipo = 'email' THEN c.contato END) AS email,
        MAX(CASE WHEN c.tipo = 'celular' THEN c.contato END) AS celular,
        MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato END) AS whatsapp,
        f.data_cadastro,
        f.data_alteracao
    FROM fornecedor f
    LEFT JOIN contato c ON c.id_fornecedor = f.id
    GROUP BY f.id, f.nome_fantasia, f.sobrenome_razao, f.cpf_cnpj, f.rg_ie, f.data_cadastro, f.data_alteracao;

    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE OR REPLACE VIEW vw_empresa_contatos AS
    SELECT 
        e.id,
        e.nome_fantasia,
        e.razao_social,
        e.cnpj,
        e.ie,
        e.senha,
        e.ativo,
        e.codigo_verificacao,
        MAX(CASE WHEN c.tipo = 'email' THEN c.contato END) AS email,
        MAX(CASE WHEN c.tipo = 'celular' THEN c.contato END) AS celular,
        MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato END) AS whatsapp,
        e.data_cadastro,
        e.data_alteracao
    FROM empresa e
    LEFT JOIN contato c ON c.id_empresa = e.id
    GROUP BY e.id, e.nome_fantasia, e.razao_social, e.cnpj, e.ie, e.data_cadastro, e.data_alteracao;

    "
    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE OR REPLACE VIEW vw_usuario_contatos AS
    SELECT 
        u.id,
        u.nome,
        u.sobrenome,
        u.cpf,
        u.rg,
        u.senha,
        u.ativo,
        u.administrador,
        u.codigo_verificacao,
        MAX(CASE WHEN c.tipo = 'email' THEN c.contato END) AS email,
        MAX(CASE WHEN c.tipo = 'celular' THEN c.contato END) AS celular,
        MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato END) AS whatsapp,
        u.data_cadastro,
        u.data_alteracao
    FROM usuario u
    LEFT JOIN contato c ON c.id_usuario = u.id
    GROUP BY u.id, u.nome, u.sobrenome, u.cpf, u.rg, u.data_cadastro, u.data_alteracao;
    "
}
  
configurar_postgresql
criar_tabelas