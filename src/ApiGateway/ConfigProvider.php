<?php

namespace ApiGateway;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
            	Admin\Client::class => Admin\ClientFactory::class,
	            Admin\Cache\Storage\MongoDb::class => Admin\Cache\Storage\MongoDbFactory::class,
                Admin\Middleware\AuthenticationMiddleware::class => Admin\Middleware\AuthenticationFactory::class,
            ],
        ];
    }
}
