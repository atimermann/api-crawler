help:
	@cat Makefile

up:
	@docker compose up -d

down:
	@docker compose down

shell:
	@echo "Entering the Docker container..."
	@docker compose exec app bash --init-file .bashrc

logs:
	@docker compose logs -f

rebuild:
	@docker compose up -d --build

infection:
	@./vendor/bin/infection

swagger-build:
	@php artisan l5-swagger:generate

docker-deploy:
	@./docker/prod/deploy.sh
