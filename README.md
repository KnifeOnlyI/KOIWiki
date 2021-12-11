# KOI Wiki

A simple web wiki manager

## Environment variables (.env.docker file)

- PORT

- APP_SECRET

- DATABASE_NAME
- DATABASE_USER
- DATABASE_PASSWORD

- SENDER_EMAIL_USER
- SENDER_EMAIL_PASSWORD
- SENDER_EMAIL_HOST
- SENDER_EMAIL_PORT

## PHP / Symfony commands

Install packages

```bash
composer install
```

Start a development server

```bash
symfony server:start
```

Execute migrations

```bash
php bin/console doctrine:migrations:migrate
```

Hash password

```bash
php bin/console security:hash-password
```

## Docker commands (production)

Start application

```bash
docker-compose --env-file .env.docker up --build -d
```

Stop application

```bash
docker-compose --env-file .env.docker stop
```

Build/Rebuild application

```bash
docker-compose --env-file .env.docker build
```

Start bash session into containers :

```bash
docker exec -ti koi-wiki_koi-wiki-app_1 bash        # PHP container
docker exec -ti koi-wiki_koi-wiki-postgresql_1 bash # PostgreSQL container
```
