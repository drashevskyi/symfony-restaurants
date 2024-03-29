# symfony-restaurants

Installation:

1. Clone repo
2. `>cd to project root` & run `>composer install`
3. Set up DB access in .env file and test DB in .env.test
4. Create tables in DB table from Entities `>php bin/console doctrine:schema:update --force`
5. Load fixtures `>php bin/console doctrine:fixtures:load` & `>php bin/console doctrine:fixtures:load --env=test` for test DB
6. Run tests `>php bin/phpunit`, expected OK (11 tests, 20 assertions)

Comments:

1. Registration not required, but possible. Initial user fixture is created;
2. KnpPaginatorBundle is used in order not to load all rows on a single page;
3. Filtering is implemented from KnpPaginatorBundle;
4. `App\Service\RestaurantService` created to declare `saveRestaurant` method that will add and update Restaurants. Method is tested in `App\Tests\Controller\RestaurantControllerTest` with passing different `$formData`;
5. API endpoint to get restaurant tables with auth layer is accessed by `/api/restaurant/table/{restaurant}?apikey=your_api_key`:
  - auth layer implemented with `App\Security\TokenAuthenticator` using api firewall and api_user_provider, which is referring to apiToken field of User entity
  - apiToken is generated while registering the user in `App\Controller\RegistrationController`
  - `App\Security\TokenAuthenticator` updated to use get parameter apikey instead of headres
  - `Symfony\Component\Serializer\Serializer` is used to serialize restaurant tables so the result is returned in json.
