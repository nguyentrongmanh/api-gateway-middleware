<?php

namespace ApiGateway\Cache\Storage;

use ApiGateway\Entity\ApiUser;

interface StorageInterface
{
	public function set(ApiUser $apiUser);

	public function get(string $userId);
}