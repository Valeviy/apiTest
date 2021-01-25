<?php

declare(strict_types=1);

namespace Test\TestApi\Manager;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Test\TestApi\Model\User;
use Test\TestApi\Response\TestTokenResponse;
use Test\TestApi\Response\TestUserResponse;

class TestApiManager
{
    public const DEFAULT_API_URL = 'http://testapi.ru';

    /**
     * @var Client
     */
    public $client;

    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $token;

    /**
     * AbstractManager constructor.
     *
     * @param array $config
     * @param Client $apiClient
     */
    public function __construct(array $config, Client $apiClient)
    {
        $this->config = $config;

        $this->client = $apiClient;
    }

    /**
     * @param string $userName
     * @return TestUserResponse
     * @throws GuzzleException
     */
    public function getUser(string $userName): TestUserResponse
    {
        if (empty($this->token)) {
            throw new \RuntimeException("Need to log in");
        }

        try {
            $response = $this->client->get(
                sprintf(
                    '%s/get-user/%s/?token=%s',
                    $this->config['api']['url'],
                    $userName,
                    $this->token,
                ),
            );
        } catch (RequestException $e) {
            $response = $e->getResponse();
            error_log('TestApi getUser exception:' . $e);
        }

        return new TestUserResponse((string) $response->getBody());
    }

    /**
     * @param int $userId
     * @param User $user
     * @return TestUserResponse
     * @throws GuzzleException
     */
    public function updateUser(int $userId, User $user): TestUserResponse
    {
        if (empty($this->token)) {
            throw new \RuntimeException("Need to log in");
        }

        try {
            $response = $this->client->post(
                sprintf(
                    '%s/user/%s/update?token=%s',
                    $this->config['api']['url'],
                    $userId,
                    $this->token,
                ),
                [
                    'json' => $user->transform(),
                ]
            );
        } catch (RequestException $e) {
            $response = $e->getResponse();
            error_log('TestApi getUser exception:' . $e);
        }

        return new TestUserResponse((string) $response->getBody());
    }

    /**
     * @throws GuzzleException
     */
    public function logIn(): void
    {
        $result = $this->getToken();

        if ($result->isSuccess() && !empty($result->getToken())) {
            $this->token = $result->getToken();
        } else {
            error_log('Log in exception:' . $result->getMessage());
            throw new \RuntimeException("Cannot log in");
        }
    }

    /**
     * @return TestTokenResponse
     * @throws GuzzleException
     */
    public function getToken(): TestTokenResponse
    {
        try {
            $response = $this->client->get(
                sprintf(
                    '%s/auth?login=%s&pass=%s',
                    $this->config['api']['url'],
                    $this->config['api']['login'],
                    $this->config['api']['pass'],
                ),
            );

        } catch (RequestException $e) {
            error_log('TestApi getToken exception:' . $e->getMessage());
            $response = $e->getResponse() ;
        }

        return new TestTokenResponse((string) $response->getBody());
    }
}