<?php

namespace ApiGateway\Entity;

class ApiUser
{
	/**
	 * api gateway internal id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * argus 3 user id
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * agent id
	 *
	 * @var int
	 */
	protected $agentId;

	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return ApiUser
	 */
	public function setId(string $id): ApiUser {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int {
		return $this->userId;
	}

	/**
	 * @param int $userId
	 *
	 * @return ApiUser
	 */
	public function setUserId(int $userId): ApiUser {
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getAgentId(): int {
		return $this->agentId;
	}

	/**
	 * @param int $agentId
	 *
	 * @return ApiUser
	 */
	public function setAgentId(int $agentId): ApiUser {
		$this->agentId = $agentId;

		return $this;
	}
}