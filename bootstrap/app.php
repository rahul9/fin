<?php
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/constant.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

 $app->withFacades();



/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/


$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);


/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('database');
$app->configure('env');
$app->configure('auth');
$app->configure('passport');
$app->configure('session');
$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(Jenssegers\Mongodb\MongodbServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(\Illuminate\Redis\RedisServiceProvider::class);
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(Illuminate\Routing\RoutingServiceProvider::class);
$app->register(SessionServiceProvider::class);
//$app->register(\League\OAuth2\Server\Lumen\OAuth2ServerServiceProvider::class);
//$app->bind(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class, \League\OAuth2\Server\Repositories\ClientRepository::class);
$app->withEloquent();


/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/
$app->routeMiddleware([
    'checkAuth' => App\Http\Middleware\CheckAuth::class,
    'auth' => App\Http\Middleware\Authenticate::class,
    'CheckClientAuth' => App\Http\Middleware\CheckClientAuth::class,
    'client' => Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    'scopes' => Laravel\Passport\Http\Middleware\CheckForScopes::class,
    'scope' => Laravel\Passport\Http\Middleware\CheckScopes::class
]);

\Dusterio\LumenPassport\LumenPassport::routes($app, ['prefix' => 'v1/oauth']);
\Dusterio\LumenPassport\LumenPassport::tokensExpireIn(Carbon::now()->addMinutes($_ENV['PASSPORT_ACCESS_TOKEN_EXPIRY_MINUTES']));
\Dusterio\LumenPassport\LumenPassport::allowMultipleTokens();


Passport::refreshTokensExpireIn(Carbon::now()->addMinutes($_ENV['PASSPORT_REFRESH_TOKEN_EXPIRY_MINUTES']));


$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
