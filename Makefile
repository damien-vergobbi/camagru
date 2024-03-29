NAME			= camagru
YML_FILE		= docker-compose.yml
ENV_FILE		= ./src/.env

all: build

build:
	mkdir -p ./src/public/posts
	chmod 777 ./src/public/posts
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) up --build --remove-orphans

stop:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) stop

down:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) down

up:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) up

clean: stop
	docker system prune -af
	docker volume prune -f
	rm -rf ./src/public/posts/*

.PHONY: all build stop down up clean
