<?php

namespace ApiGateway\Admin\Cache\Storage;

use Interop\Container\ContainerInterface;

class MongoDbFactory
{
    public function __invoke(ContainerInterface $container)
    {
    	$config = $container->get('config')['api-gateway']['cache'] ?? [];

	    $client = new \MongoDB\Client($config['uri']);

	    return new MongoDb(
    		$client,
		    $client->{$config['database'] ?? 'api_gateway_cache'}->{$config['collection'] ?? 'user'}
	    );
    }
}
