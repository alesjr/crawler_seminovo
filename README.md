# Crawler Seminovo
## Install 

Execute o comando composer install

## Expecificação:

PHP 7.2 (módulos curl e mysql)</br>
Mysql 5.7

## Instalar a base de dados:

Execute o arquivo tabela.sql</br>
Alterar o arquivo na pasta src/settings.php, os seguintes valores (conforme for configurado):</br>
	"db" => [</br>
            "host"   => "host", //lugar onde a base de dados está</br>
            "dbname" => "seminovos", //nome do banco</br>
            "user"   => "root", //usuário do banco</br>
            "pass"   => "minha senha" //senha do banco de dados</br>
        ]
