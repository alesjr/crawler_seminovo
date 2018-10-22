# Crawler Seminovo
## Install 

Execute o comando composer install

## Expecificação:

PHP 7.2 (módulos curl e mysql)
Mysql 5.7

## Instalar a base de dados:

Execute o arquivo tabela.sql
Alterar o arquivo na pasta src/settings.php, os seguintes valores (conforme for configurado):
	"db" => [
            "host"   => "host", //lugar onde a base de dados está
            "dbname" => "seminovos", //nome do banco
            "user"   => "root", //usuário do banco
            "pass"   => "minha senha" //senha do banco de dados
        ],
