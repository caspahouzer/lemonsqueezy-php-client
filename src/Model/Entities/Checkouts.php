<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Checkouts extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('checkouts')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'checkouts';
    }
}
