<?php

declare(strict_types=1);

namespace Test\TestApi\Response;

/**
 * Class TestTokenResponse
 */
class TestTokenResponse extends AbstractResponse
{
    /**
     * @var string|null
     */
    protected $token;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return TestTokenResponse
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

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

        if (array_key_exists('token', $jsonResponse)) {
            $this->setToken($jsonResponse['token']);
        }
    }
}

