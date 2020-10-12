<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $username;

    private $roles = [];

    /**
     * @var string The hashed password
     */
    private $password;
    /**
     * @var int
     */
    private $played = 0;
    /**
     * @var int
     */
    private $pints = 0;
    /**
     * @var int
     */
    private $bonusPoints = 0;
    /**
     * @var int
     */
    private $points = 0;
    /**
     * @var int
     */
    private $currentPosition;
    /**
     * @var int
     */
    private $previousPosition;


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlayed(): int
    {
        return $this->played;
    }

    public function getPints(): int
    {
        return $this->pints;
    }

    public function getBonusPoints(): int
    {
        return $this->bonusPoints;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function hasMovedUp(): bool
    {
        return !is_null($this->previousPosition) && $this->previousPosition > $this->currentPosition;
    }

    public function hasMovedDown(): bool
    {
        return !is_null($this->previousPosition) && $this->previousPosition < $this->currentPosition;
    }

    public function setTableData(int $played, int $pints, int $bonusPoints, int $points, int $position)
    {
        $this->played = $played;
        $this->pints = $pints;
        $this->bonusPoints = $bonusPoints;
        $this->points = $points;
        $this->currentPosition = $position;
    }

    public function setPreviousPosition(int $position)
    {
        $this->previousPosition = $position;
    }
}
