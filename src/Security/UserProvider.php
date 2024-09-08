<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $users;
    /**
     * @var string
     */
    private $defaultPassword;

    public function __construct(string $defaultPassword)
    {
        $this->users = [
            'Andy',
            'Blochy',
            'Deadly',
            'Gav',
            'Just',
            'Smudger',
            'Stu',
        ];
        $this->defaultPassword = $defaultPassword;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UserNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if (!in_array($username, $this->users)) {
            throw new UserNotFoundException('User not found');
        }
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->defaultPassword);

        return $user;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    public function getUsers()
    {
        return array_map([$this, 'loadUserByUsername'], $this->users);
    }

    public function listUsernames()
    {
        return array_combine($this->users, $this->users);
    }
}
