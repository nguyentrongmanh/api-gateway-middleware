<?php

namespace ApiGateway\Admin\Cache\Storage;

use ApiGateway\Admin\Entity\ApiUser;

class NullStorage implements StorageInterface
{
	public function __construct(array $config)
	{
	}

	public function set(ApiUser $apiUser)
    {
		// nothing to do
	}

	public function get(string $userId)
    {
		return false;
	}

	public function remove(ApiUser $apiUser)
    {
        // nothing to do
    }
}