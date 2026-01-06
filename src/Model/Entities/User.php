<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class User extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('users')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'users';
    }

    /**
     * Get the user's name
     *
     * Returns the full name associated with this user account.
     *
     * @return ?string The user's name, or null if not set
     * @since 1.0.0
     */
    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    /**
     * Get the user's email address
     *
     * Returns the email address associated with this user account.
     *
     * @return ?string The user's email address, or null if not set
     * @since 1.0.0
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }
}
