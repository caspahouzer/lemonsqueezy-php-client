<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Discounts extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('discounts')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'discounts';
    }
}
