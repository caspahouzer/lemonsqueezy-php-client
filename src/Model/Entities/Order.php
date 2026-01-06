<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Order extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('orders')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'orders';
    }
}
