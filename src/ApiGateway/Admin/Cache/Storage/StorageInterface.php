<?php

namespace ApiGateway\Admin\Cache\Storage;

use ApiGateway\Admin\Entity\ApiUser;

interface StorageInterface
{
	public function set(ApiUser $apiUser);

	public function get(string $userId);

	public function remove(ApiUser $apiUser);
}