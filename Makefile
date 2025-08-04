up:
	docker compose up -d

stop:
	docker compose stop

down:
	docker compose down

build:
	docker compose build

build-no-cache:
	docker compose build --no-cache

cli:
	docker compose exec -it php_booking bash