@echo off
php %~dp0\bin\phpDocumentor.phar -d "./module" -t "./docs/api" --template="responsive-twig" -e "php" %*