<?php

declare(strict_types=1);

namespace Test\TestApi\Response;

use Test\TestApi\Model\User;

/**
 * Class TestUserResponse
 */
class TestUserResponse extends AbstractResponse
{
    /**
     * @var User|null
     */
    protected $userData;

    /**
     * @return User|null
     */
    public function getUserData(): ?User
    {
        return $this->userData;
    }

    /**
     * @param User $userData
     *
     * @return TestUserResponse
     */
    public function setUserData(User $userData): self
    {
        $this->userData = $userData;

        return $this;
    }


    /**
     *
     * @param string $data
     */
    protected function load(string $data): void
    {
        $jsonResponse = json_decode($data, true);

        if (array_key_exists('status', $jsonResponse)) {
            $this->setSuccess('OK' === $jsonResponse['status']);
        }

        $this->setMessage($jsonResponse['status']);

        if (
            array_key_exists('id', $jsonResponse)
            ||  array_key_exists('active', $jsonResponse)
            ||  array_key_exists('blocked', $jsonResponse)
            ||  array_key_exists('name', $jsonResponse)
            ||  array_key_exists('permissions', $jsonResponse)
            ||  array_key_exists('created_at', $jsonResponse)
        ) {
            $this->loadUserData($jsonResponse);
        }
    }

    /**
     * @param array $userData
     */
    private function loadUserData(array $userData): void
    {
        $user = new User();

        if (array_key_exists('id', $userData)) {
            $user->setId((int) $userData['id']);
        }

        if (array_key_exists('active', $userData)) {
            $user->setActive((int) $userData['active']);
        }

        if (array_key_exists('blocked', $userData)) {
            $user->setBlocked((bool) $userData['blocked']);
        }

        if (array_key_exists('created_at', $userData)) {
            $user->setCreatedAt((int) $userData['created_at']);
        }

        if (array_key_exists('name', $userData)) {
            $user->setName((string) $userData['name']);
        }

        if (array_key_exists('permissions', $userData)) {
            $user->setPermissions($userData['permissions']);
        }

        $this->setUserData($user);
    }
}

