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
    CREATE TABLE IF NOT EXISTS usuario (
        id BIGSERIAL PRIMARY KEY,
        nome TEXT,
        sobrenome TEXT,
        cpf TEXT,
        rg TEXT,
        senha TEXT,
        ativo BOOLEAN DEFAULT FALSE,
        administrador BOOLEAN DEFAULT FALSE,
        codigo_verificacao TEXT,
        data_cadastro TIMESTAMP DEFAULT NOW(),
        data_alteracao TIMESTAMP DEFAULT NOW()
    );
    "

    sudo -u postgres psql -d "$NOME_BANCO" -c "
    CREATE TABLE IF NOT EXISTS contato (
        id BIGSERIAL PRIMARY KEY,
        id_usuario BIGINT REFERENCES usuario(id),
        tipo TEXT,
        contato TEXT,
        data_cadastro TIMESTAMP DEFAULT NOW(),
        data_alteracao TIMESTAMP DEFAULT NOW()
    );
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
    GROUP BY 
        u.id, u.nome, u.sobrenome, u.cpf, u.rg,
        u.senha, u.ativo, u.administrador,
        u.codigo_verificacao,
        u.data_cadastro, u.data_alteracao;
    "
}

configurar_postgresql
criar_tabelas