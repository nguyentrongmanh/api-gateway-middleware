<?php

namespace ApiGateway;

use Interop\Container\ContainerInterface;

class ClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
    	$config = $container->get('config')['api-gateway'] ?? [];

        $client = new Client();
	    $client->setEndpoint($config['endpoint']);
        $client->setAdminAuthToken($config['adminAuthToken'] ?? null);
	    $client->setKey($config['apiKey'] ?? null);

	    if ($config['cache'] ?? false) {
		    if ($container->has($config['cache']['storage'])) {
			    $client->setCache($container->get($config['cache']['storage']));
		    }
	    }

	    return $client;
    }
}
