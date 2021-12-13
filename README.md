# KOI Wiki

A simple web wiki manager

## Environment variables (.env.docker file)

```bash
PORT=""

APP_NAME=""
APP_SECRET=""

DATABASE_NAME=""
DATABASE_USER=""
DATABASE_PASSWORD=""

EMAIL_SENDER_USER=""
EMAIL_SENDER_PASSWORD=""
EMAIL_SENDER_HOST=""
EMAIL_SENDER_PORT=""
```

## Available roles

- ROLE_ARTICLE_CREATE
- ROLE_ARTICLE_VIEW_PRIVATE
- ROLE_ARTICLE_DELETE_PRIVATE
- ROLE_ARTICLE_CATEGORY_CREATE
- ROLE_ARTICLE_CATEGORY_EDIT
- ROLE_ARTICLE_CATEGORY_DELETE

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

## Database commands

```bash
# Create an admin user
INSERT INTO "user" ("id", "email", "roles", "password", "is_verified") 
VALUES (1, '<email>', '["ROLE_ARTICLE_CREATE", "ROLE_ARTICLE_VIEW_PRIVATE", "ROLE_ARTICLE_DELETE_PRIVATE", "ROLE_ARTICLE_CATEGORY_CREATE", "ROLE_ARTICLE_CATEGORY_EDIT", "ROLE_ARTICLE_CATEGORY_DELETE"]', '<password>', true);
```

```bash
# Reset user id sequence
ALTER SEQUENCE user_id_seq RESTART WITH <next_value>;
```

## Docker commands (production)

Start application

```bash
docker-compose --env-file .env.docker up -d         # Not rebuild containers
docker-compose --env-file .env.docker up --build -d # Rebuild containers
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
