# API Gateway Middleware

this library provides PSR-7 middleware in order to get detailed user information by api umbrella's HTTP Header `X_API_USER_ID`

## Configuration

#### configure dependencies


```php
<?php 
 return [
 	'api-gateway' => [
 		'endpoint' => 'https://api.go-suite.com/api-umbrella/v1/',
 		'adminAuthToken' => '<<TOKEN>>',
 		'apiKey' => '<<API KEY>>',
 		'cache' => [
 			'storage' => 'ApiGateway\Cache\Storage\MongoDb',
 			'uri' => 'mongodb://127.0.0.1',
 			'database' => 'api_gateway_cache',
 			'collection' => 'user',
 		],
 	],
 ];
 ```
 
Activate ConfigProvider
```php
<?php
// config/config.php


$aggregator = new ConfigAggregator([
    Argus\ConfigProvider::class,
    ...
    ApiGateway\ConfigProvider::class,
    ...
], $cacheConfig['config_cache_path']);

```

#### add middleware to pipeline
there are two possibilities to configure the project using the `AuthenticationMiddleware`.

1) add it to to the pipeline to get the middleware be called at _every_ request.

```php
// Register the routing middleware in the middleware pipeline
$app->pipeRoutingMiddleware();
...
$app->pipe(AuthenticationMiddleware::class)
...
```

2) add it to some of the routes.

```php
$app->route(
	'/api/customer[/{id:\d+}]',
	ApiGateway\Middleware\AuthenticationMiddleare::class,
	Argus\Action\Customer\Resource::class,
	['GET','POST','PUT','PATCH'],
	'customer'
);
```

## Usage
with a proper configuration you can access the `ApiGateway\Entity\ApiUser` within your action/middleware like the following:

```php
public function process(ServerRequestInterface $request, DelegateInterface $delegate)
{
    $apiUser = $request->getAttribute(ApiUser::class);
}
```
