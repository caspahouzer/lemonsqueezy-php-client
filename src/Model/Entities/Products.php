<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Products extends AbstractModel
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
}
