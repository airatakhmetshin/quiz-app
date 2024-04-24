# quiz-app

> Простая система тестирования поддерживающая вопросы с нечеткой логикой и возможностью выбора нескольких вариантов ответа.
>
> Что такое вопросы с нечеткой логикой? “2 + 2 = ”
> 1. 4
> 2. 3+1
> 3. 10
>
> Правильными ответами тут будут 1 ИЛИ 2 ИЛИ (1 И 2). При этом любые другие комбинации (например, 1 И 3) не будут считаться верными, несмотря на то, что содержат правильный ответ.

### Setup
```shell
cp docker-compose.override.yml.dist docker-compose.override.yml
docker-compose up --build -d
docker-compose exec quiz_app sh
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Open: http://localhost:80

### Schema

![schema](docs/schema.png)
