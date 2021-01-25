<?php

declare(strict_types=1);

namespace Test\TestApi\Model;

/**
 * Class User
 */
class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $active;

    /**
     * @var bool|null
     */
    private $blocked;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var array|null
     */
    private $permissions;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getActive(): ?int
    {
        return $this->active;
    }

    /**
     * @param int|null $active
     *
     * @return User
     */
    public function setActive(?int $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     *
     * @return User
     */
    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    /**
     * @param bool|null $blocked
     *
     * @return User
     */
    public function setBlocked(?bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return User
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    /**
     * @param array|null $permissions
     *
     * @return User
     */
    public function setPermissions(?array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }
    /**
     * @return array
     */
    public function transform(): array
    {
        return [
            'active' => $this->active,
            'blocked' => $this->blocked,
            'name' => $this->name,
            'permissions' => $this->transformPermissions()
        ];
    }

    /**
     * @return array
     */
    public function transformPermissions(): array
    {
        $permissionsStructure = [];

        foreach ($this->permissions as $permission) {
            array_push($permissionsStructure, [
                'id' => $permission['id'],
                "permission" => $permission['comment']
            ]);
        }

        return $permissionsStructure;
    }
}
