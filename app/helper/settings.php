<?php
session_start();
# Simulando um usuário logado
$_SESSION['usuario'] =[
    'nome' => 'Junior',
    'cpf' => '123.456.789-00'
];

# Caminho raiz do projeto
define('ROOT', dirname(__FILE__, 3));
# Extensão dos arquivos de view
define('EXT_VIEW', '.html');
# Diretório das views
define('DIR_VIEW', ROOT . '/app/view/');
#$_SERVER['HTTP_HOST'] : indica dominio (host) que foi chamado na url pelo navegador. Dominio principal meusite.com.br
#$_SERVER['REQUEST_SCHEME'] : indica o protocolo utilizado na requisição (http ou https)
#criamos uma constante HOME que representa a url raiz do projeto
define('HOME', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');