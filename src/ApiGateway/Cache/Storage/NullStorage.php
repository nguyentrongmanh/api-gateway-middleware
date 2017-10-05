<?php

namespace ApiGateway\Cache\Storage;

use ApiGateway\Entity\ApiUser;

class NullStorage implements StorageInterface
{
	public function __construct(array $config)
	{
	}

	public function set(ApiUser $apiUser) {
		// nothing to do
	}

	public function get(string $userId) {
		return false;
	}
}