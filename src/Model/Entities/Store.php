<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Store extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('stores')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'stores';
    }

    /**
     * Get the store's name
     *
     * Returns the human-readable name of the store.
     *
     * @return ?string The store's name, or null if not set
     * @since 1.0.0
     */
    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }
}
