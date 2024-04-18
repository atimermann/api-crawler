#!/bin/bash
set -e

# Executa a geração da documentação Swagger
php artisan l5-swagger:generate
php artisan migrate --force

# Executa o servidor Apache em foreground
exec apache2-foreground
