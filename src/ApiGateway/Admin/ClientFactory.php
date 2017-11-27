<?php

namespace ApiGateway\Admin;

use Interop\Container\ContainerInterface;

class ClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $mongoConfig = $container->get('config')['api-gateway']['cache'] ?? [];

        $mongoClient = new \MongoDB\Client($mongoConfig['uri']);
        $userAgentCollection = $mongoClient->{$mongoConfig['database'] ?? 'api_gateway_cache'}->{$mongoConfig['userAgencyCollection'] ?? 'user_agent'};

    	$config = $container->get('config')['api-gateway'] ?? [];

        $client = new Client($userAgentCollection);
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
