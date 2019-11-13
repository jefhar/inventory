build:
	docker build -t registry.gitlab.com/c11k/c11k .

docker:
	docker-compose up -d --build

down:
	docker-compose down

stop:
	docker-compose stop

start:
	docker-compose start

clean:
	docker system prune

composerinstall:
	docker run --rm -v "$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer install'

composerupdate:
	docker run --rm -v "$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer update'

checkcomposer:
	docker run --rm -v "$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && vendor/bin/security-checker security:check composer.lock'

ci:
	docker login registry.gitlab.com
	gitlab-runner exec docker test

yarninstall:
	docker run --rm -v "$(CURDIR):/app:delegated" node:12-slim sh -c 'cd /app && yarn install'

npmdev:
	docker run --rm -v "$(CURDIR):/app:delegated" node:12-slim sh -c 'cd /app && npm run development'

npmprod:
	docker run --rm -v "$(CURDIR):/app:delegated" node:12-slim sh -c 'cd /app && npm run production'

npmwatch:
	docker run --rm -v $(CURDIR):/app:delegated node:12-slim sh -c 'cd /app && npm run development -- --watch'

swagger:
	docker run --rm -p 8088:8080 swaggerapi/swagger-editor

phpcs:
	docker run --rm -v "$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer phpcs'

phpcbf:
	docker run --rm -v "$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer phpcbf'

pretest:
	rm -rf tests/coverage*
	docker run --rm -v"$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer pretest'

test:
	docker run --rm -v"$(CURDIR):/app:delegated" registry.gitlab.com/c11k/c11k sh -c 'cd /app && composer pretest && composer test'

testdusk:
	docker-compose exec php-fpm php artisan dusk
