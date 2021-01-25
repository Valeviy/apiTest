<?php

declare(strict_types=1);

namespace Test\TestApi\Response;

/**
 * Class AbstractResponse
 */
abstract class AbstractResponse
{
    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     *
     * @return AbstractResponse
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * AbstractResponse constructor.
     *
     * @param string $response
     */
    public function __construct(string $response)
    {
        if ($this->isJson($response)) {
            $this->load($response);
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     *
     * @return AbstractResponse
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isJson(string $string): bool
    {
        json_decode($string, true);

        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * @param string $response
     */
    abstract protected function load(string $response): void;
}

