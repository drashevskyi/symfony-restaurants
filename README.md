# symfony-restaurants

Installation:

1. Copy files from repo
2. `>cd to project root` & run `>composer install`
3. Set up DB access in .env file and test DB in .env.test
4. Create tables in DB table from Entities: `php bin/console doctrine:schema:update --force`
5. Load fixtures `php bin/console doctrine:fixtures:load` & `php bin/console doctrine:fixtures:load --env=test` for test DB
6. Run tests `>php bin/phpunit`, expected OK (11 tests, 20 assertions)
