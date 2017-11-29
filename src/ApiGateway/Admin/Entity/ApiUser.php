<?php

namespace ApiGateway\Admin\Entity;

use ApiGateway\Exception\RuntimeException;
use stdClass;

class ApiUser
{
    /**
     * api gateway internal id
     *
     * @var string
     */
    public $id;

    /**
     * argus 3 user id
     *
     * @var int
     */
    public $userId;

    /**
     * API key
     *
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string[]
     */
    public $roles = array();

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return (array) $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }


    /**
     * @param stdClass $jsonObject
     *
     * @return ApiUser
     */
    public function parse(stdClass $jsonObject)
    {
        if (!isset($jsonObject->user)) {
            throw new RuntimeException('invalid api gateway response. does not contain user information');
        }

        if (preg_match('~^([0-9]*)@~', $jsonObject->user->email, $m)) {
            $this->userId = (int)$m[1];
        }

        $this->id = $jsonObject->user->id;
        $this->firstName = $jsonObject->user->first_name;
        $this->lastName = $jsonObject->user->last_name;
        $this->email = $jsonObject->user->email;
        $this->apiKey = $jsonObject->user->api_key;
        $this->roles = $jsonObject->user->roles;

        return $this;
    }
}
