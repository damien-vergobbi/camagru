NAME			= camagru
YML_FILE		= docker-compose.yml
ENV_FILE		= ./src/.env

all: build

build:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) up

stop:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) stop

down:
	docker-compose -p $(NAME) -f $(YML_FILE) --env-file $(ENV_FILE) down

clean: down
	docker system prune -af
	docker volume prune -f
	rm -rf ./src/public/posts/

.PHONY: all build stop down clean
