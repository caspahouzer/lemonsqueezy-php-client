<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Customer extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('customers')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'customers';
    }

    /**
     * Get the customer's email address
     *
     * Returns the email address associated with this customer.
     *
     * @return ?string The customer's email address, or null if not set
     * @since 1.0.0
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }
}
