<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Product extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('products')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'products';
    }

    /**
     * Get the product's name
     *
     * Returns the human-readable name of the product.
     *
     * @return ?string The product's name, or null if not set
     * @since 1.0.0
     */
    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }
}
