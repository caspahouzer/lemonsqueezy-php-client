<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Prices extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('prices')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'prices';
    }
}
